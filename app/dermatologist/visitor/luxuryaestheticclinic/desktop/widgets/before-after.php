<?php

$beforeAfterData = getGalleryWithCategories(
    $connection,
    'gallery',
    'gallery',
    true,
    5,
    1
);

if (!empty($beforeAfterData)) :
?>

    <section class="transformations-section" id="before-after">

        <div class="container text-center reveal active">
            <span class="section-tag">Clinical Proof & Results</span>
            <h2>Real Transformations, Beautiful Success</h2>
            <p class="max-width-600">
                Review real, untouched before and after treatment records.
                Slide to evaluate skin texture transformations achieved by our
                customized clinical programs.
            </p>
        </div>

        <div class="container transform-filter-container reveal active">

            <?php
            $isFirst = true;
            foreach ($beforeAfterData as $index => $category) :

                if (empty($category['gallerys'])) {
                    continue;
                }
            ?>

                <button
                    type="button"
                    class="filter-btn <?= ($isFirst ? 'active' : ''); ?>"
                    data-category="category_<?= $category['category_id']; ?>">

                    <?= htmlspecialchars($category['category_title']); ?>

                </button>

            <?php
                $isFirst = false;
            endforeach; ?>

        </div>

        <?php
        $isFirst = true;
        foreach ($beforeAfterData as $index => $category) :

            if (empty($category['gallerys'])) {
                continue;
            }

            $item = $category['gallerys'][0];

            $galleryImageList = json_decode($item['gallery_image_list'], true);
            $beforeImage = $galleryImageList[0]['path'];
            $afterImage = $galleryImageList[1]['path'];

        ?>

            <div
                class="container transform-container-grid transformation-panel <?= ($isFirst ? 'active' : ''); ?>"
                data-category="category_<?= $category['category_id']; ?>">

                <div class="before-after-wrapper reveal active">

                    <div class="slider-img slider-img-after">
                        <img
                            src="<?= $beforeImage ?>"
                            alt="<?= htmlspecialchars($item['gallery_title']); ?> After">

                        <span class="slider-badge badge-after">
                            After
                        </span>
                    </div>

                    <div class="slider-img slider-img-before">
                        <img
                            src="<?= $afterImage ?>"
                            alt="<?= htmlspecialchars($item['gallery_title']); ?> Before">

                        <span class="slider-badge badge-before">
                            Before
                        </span>
                    </div>

                    <div class="slider-handle">
                        <button
                            type="button"
                            class="slider-handle-button">

                            <i data-lucide="chevron-left"></i>
                            <i data-lucide="chevron-right"></i>

                        </button>
                    </div>

                </div>

                <div class="transform-details-card reveal active">

                    <?php if (!empty($item['gallery_description']) && strip_tags($item['gallery_description']) !== $item['gallery_description']) : ?>

                        <?= $item['gallery_description']; ?>

                    <?php else : ?>

                        <?php if (!empty($item['gallery_description'])) : ?>
                            <p class="transform-details-desc">
                                <?= htmlspecialchars($item['gallery_description']); ?>
                            </p>
                        <?php endif; ?>

                        <div class="transform-metrics">

                            <?php if (!empty($item['gallery_duration'])) : ?>
                                <div class="metric-item">
                                    <h4 class="metric-item-num">
                                        <?= htmlspecialchars($item['gallery_duration']); ?>
                                    </h4>
                                    <span class="metric-item-lbl">
                                        Treatment Span
                                    </span>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($item['gallery_technology'])) : ?>
                                <div class="metric-item">
                                    <h4 class="metric-item-num">
                                        <?= htmlspecialchars($item['gallery_technology']); ?>
                                    </h4>
                                    <span class="metric-item-lbl">
                                        Clinical Technology
                                    </span>
                                </div>
                            <?php endif; ?>

                        </div>

                        <div style="margin-top:4.5rem">
                            <a
                                href="<?= htmlspecialchars($item['gallery_url']); ?>"
                                class="btn btn-primary">

                                Achieve Similar Results

                            </a>
                        </div>

                    <?php endif; ?>

                </div>

            </div>

        <?php
            $isFirst = false;
        endforeach; ?>

    </section>

<?php endif;

?>