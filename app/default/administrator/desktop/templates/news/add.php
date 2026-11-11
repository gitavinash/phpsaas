<?php
require_once FUNCTIONS . 'duplicate.php';

$dupSourceId = iswGetDuplicateSourceId();
$dupData = ($dupSourceId > 0) ? iswGetDuplicateRecord($connection, 'news', $dupSourceId, SITE_ID) : null;
$dupRow = ($dupData !== null) ? $dupData['row'] : array();
$dupTitle = ($dupData !== null) ? (string)$dupData['duplicate_title'] : '';
$dupUrl = ($dupData !== null) ? (string)$dupData['duplicate_slug'] : '';
$dupDesc = ($dupData !== null) ? html_entity_decode((string)($dupRow['news_description'] ?? ''), ENT_QUOTES, 'UTF-8') : '';
$dupPreloadedGallery = ($dupData !== null) ? $dupData['preloaded_gallery'] : array();
$dupGallerySource = ($dupData !== null) ? (string)$dupData['encoded_source_id'] : '';
$isGalleryMode = (isset($theme) && $theme == 'gallery') || !empty($dupPreloadedGallery);
$newsTypeValue = isset($theme) ? (string)$theme : ($isGalleryMode ? 'gallery' : 'news');
$dupMetaTitle = (string)($dupRow['meta_title'] ?? '');
$dupMetaDesc = (string)($dupRow['meta_description'] ?? '');
$dupMetaKey = (string)($dupRow['meta_keyword'] ?? '');
$dupCanonical = (string)($dupRow['meta_canonical'] ?? '');
$dupRobots = (string)($dupRow['meta_robots'] ?? '');
$dupFbImg = (string)($dupRow['fb_img'] ?? '');
$dupFbTitle = (string)($dupRow['fb_title'] ?? '');
$dupFbDesc = (string)($dupRow['fb_desc'] ?? '');
$dupTwImg = (string)($dupRow['tw_img'] ?? '');
$dupTwTitle = (string)($dupRow['tw_title'] ?? '');
$dupTwDesc = (string)($dupRow['tw_desc'] ?? '');
$dupAudioFile = (string)($dupRow['news_audio'] ?? '');
$dupIsFeatured = ((string)($dupRow['link_is_featured'] ?? '') === '1');
$selectedCategoryId = ($dupData !== null) ? (int)($dupRow['news_category'] ?? 0) : 0;
$dupPaid = ($dupData !== null) ? (int)($dupRow['news_paid'] ?? 0) : 0;
$dupAmount = (string)($dupRow['news_price'] ?? '');
$dupTagsCsv = ($dupData !== null) ? (string)$dupData['tags_csv'] : '';
$dupAuthors = ($dupData !== null && isset($dupData['authors']) && is_array($dupData['authors'])) ? $dupData['authors'] : array();
$dupFeatureImg = (string)($dupRow['news_image'] ?? '');
$dupThumbImg = (string)($dupRow['news_thumbnail'] ?? '');
?>
<div class="product-add-ui">
    <h2>Add new <?php echo rtrim(ltrim(ADMIN_BASE_URL, '/'), '/'); ?></h2>
    <?php if ($dupData !== null) { ?>
        <div class="duplicate-mode-banner">Duplicating: <strong><?php echo iswEscHtml($dupData['source_title']); ?></strong> &mdash; Review your changes and click Publish to save a new copy.</div>
    <?php } ?>

    <div class="form-box">
        <form method="POST" id="formData" enctype="multipart/form-data">
            <div class="left-box">
                <div class="form-group">
                    <input type="text" id="title" class="form-control inside-shadow" name="title" placeholder="Title"
                        value="<?php echo iswEscHtml($dupTitle); ?>" autofocus required>
                </div>
                <div class="form-group">
                    <div class="url">
                        <p><strong>Permalink</strong></p>
                        <p><span><?php echo SITE_DOMAIN ?>/</span></p>
                        <input type="text" id="url" class="form-control" name="url" value="<?php echo iswEscHtml($dupUrl); ?>">
                    </div>
                </div>
                <div class="form-group fullscreen-editor">
                    <textarea name="description" class="editor inside-shadow"><?php echo iswEscHtml($dupDesc); ?></textarea>
                </div>

                <?php if ($isGalleryMode) { ?>

                        <!-- gallery description and other things -->

                        <section style="margin-bottom:20px">
                            <h3>Manage images</h3>
                            <div class="productGallery generalTab">
                                <div class="input-field">
                                    <div class="input-images" style="padding-top: .5rem;"></div>
                                </div>
                                <input type="hidden" name="duplicateGallerySource" value="<?php echo iswEscHtml($dupGallerySource); ?>">
                                <?php if (!empty($dupPreloadedGallery)) { ?>
                                    <script>var oldGalleryImages = <?php echo json_encode($dupPreloadedGallery); ?>;</script>
                                <?php } ?>
                            </div>
                        </section>
                <?php } ?>

                <!-- SEO & social media marketing -->

                <section>
                    <h3>Advance Options for digital marketing</h3>
                    <ul class='tabs'>
                        <li id="seo" class='active'>Search Engine Optimization</li>
                        <li id="fb">Facebook</li>
                        <li id="tw">Twitter</li>
                    </ul>
                    <div class="seo generalTab">
                        <div class="form-group">
                            <label>Meta Title</label>
                            <input type="text" name="meta_title" id="meta_title" class="form-control inside-shadow"
                                value="<?php echo iswEscHtml($dupMetaTitle); ?>" placeholder="Meta title">
                        </div>
                        <div class="form-group">
                            <label>Meta Description</label>
                            <textarea name="meta_desc" id="meta_desc" class="form-control inside-shadow"
                                placeholder="Meta Description"><?php echo iswEscHtml($dupMetaDesc); ?></textarea>
                        </div>
                        <div class="form-group">
                            <label>Meta Keywords</label>
                            <textarea name="meta_key" id="meta_keywords" class="form-control inside-shadow"
                                placeholder="Enter keywords of the post ( use , to seprate keywords )"><?php echo iswEscHtml($dupMetaKey); ?></textarea>
                        </div>
                        <div class="form-group">
                            <label>Canonical URL</label>
                            <input type="text" name="canonical" class="form-control inside-shadow" id="canonical"
                                value="<?php echo iswEscHtml($dupCanonical); ?>" placeholder="Actual URL of the <?php echo $postType ?>">
                        </div>
                        <div class="form-group">
                            <label>Allow search engines to show this Post in search results?</label>
                            <select name="robots" class="form-control inside-shadow">
                                <option value="index, follow" <?php echo (($dupRobots === 'index, follow') ? 'selected' : ''); ?>>Yes</option>
                                <option value="noindex, nofollow" <?php echo (($dupRobots === 'noindex, nofollow') ? 'selected' : ''); ?>>No</option>
                            </select>
                        </div>
                    </div>
                    <div class="fb generalTab" style="display: none;">
                        <div class="facebookPreviewBox" style="display: <?php echo ($dupFbImg !== '') ? 'block' : 'none'; ?>;">
                            <input type="hidden" name="oldFacebookImage" value="<?php echo iswEscHtml($dupFbImg); ?>">
                            <div class="img"><img src="<?php echo ($dupFbImg !== '') ? iswPreviewImageUrl($dupFbImg) : ''; ?>" id="facebookPath" alt=""></div>
                            <p id="facebookChangeImage" class='changeImage'>Remove facebook feature image</p>
                        </div>
                        <div class="facebookUploadBox" style="display: <?php echo ($dupFbImg !== '') ? 'none' : 'block'; ?>;">
                            <span>Set facebook feature image</span>
                            <input type='file' id="facebookImage" name='facebookImage' />
                        </div>
                        <div class="form-group">
                            <label>Facebook Title</label>
                            <input type="text" name="fb_title" id="fb_title" class="form-control inside-shadow"
                                value="<?php echo iswEscHtml($dupFbTitle); ?>" placeholder="Facebook title">
                        </div>
                        <div class="form-group">
                            <label>Facebook Description</label>
                            <textarea name="fb_desc" id="fb_desc" class="form-control inside-shadow"
                                placeholder="Facebook Description"><?php echo iswEscHtml($dupFbDesc); ?></textarea>
                        </div>
                    </div>
                    <div class="tw generalTab" style="display: none;">
                        <div class="twitterPreviewBox" style="display: <?php echo ($dupTwImg !== '') ? 'block' : 'none'; ?>;">
                            <input type="hidden" name="oldTwitterImage" value="<?php echo iswEscHtml($dupTwImg); ?>">
                            <div class="img"><img src="<?php echo ($dupTwImg !== '') ? iswPreviewImageUrl($dupTwImg) : ''; ?>" id="twitterPath" alt=""></div>
                            <p id="twitterChangeImage" class="changeImage">Remove twitter feature image</p>
                        </div>
                        <div class="twitterUploadBox" style="display: <?php echo ($dupTwImg !== '') ? 'none' : 'block'; ?>;">
                            <span>Set Twitter feature image</span>
                            <input type='file' id="twitterImage" name='twitterImage' />
                        </div>
                        <div class="form-group">
                            <label>Twitter Title</label>
                            <input type="text" name="tw_title" id="tw_title" class="form-control inside-shadow"
                                value="<?php echo iswEscHtml($dupTwTitle); ?>" placeholder="Twitter title">
                        </div>
                        <div class="form-group">
                            <label>Twitter Description</label>
                            <textarea name="tw_desc" id="tw_desc" class="form-control inside-shadow"
                                placeholder="Twitter Description"><?php echo iswEscHtml($dupTwDesc); ?></textarea>
                        </div>
                    </div>
                </section>

            </div>
            <div class="right-box">
                <div class="action-box inside-shadow">
                    <input type="button" id="publishPost" class="btn btn-green btn-block" value="Publish">
                    <input type="button" id="draftPost" class="btn btn-yellow btn-block" value="Save Draft">
                    <div class="form-group schedule-post-box">
                        <label>Schedule publish date &amp; time</label>
                        <input type="datetime-local" id="scheduleAt" name="schedule_at"
                            class="form-control inside-shadow" placeholder="Select date and time (YYYY-MM-DD HH:MM)"
                            title="Select date and time" min="<?php echo date('Y-m-d\\TH:i'); ?>" autocomplete="off"
                            inputmode="none">
                        <small class="schedule-help">Select date and time (YYYY-MM-DD HH:MM)</small>
                    </div>
                    <input type="button" id="schedulePost" class="btn btn-blue btn-block" value="Schedule">
                    <input type="hidden" name="type" value="<?php echo rtrim(ltrim(ADMIN_BASE_URL, '/'), '/'); ?>">
                    <input type="hidden" name="newsType" value="<?php echo iswEscHtml($newsTypeValue); ?>">
                    <input type="hidden" name="action" value="publish">
                </div>
                <div class="form-group">
                    <h2>Audio File</h2>
                    <div class="form-group audioFileUploadBox upload-btn">
                        <span id="audioFileTrigger" role="button" tabindex="0">Select Audio File</span>
                        <input type="file" id="audioFile" name="audioFile" style="display: none;"
                            accept="audio/mpeg,audio/mp3,audio/wav,audio/x-wav,audio/mp4,audio/x-m4a,audio/aac,audio/ogg,audio/webm">
                        <p id="audioFileName" style="margin-top: 20px; display: none;"></p>
                    </div>
                    <input type="hidden" name="oldAudioFile" value="<?php echo iswEscHtml($dupAudioFile); ?>">
                    <?php if ($dupAudioFile !== '') { ?>
                        <div class="duplicate-media-preview">
                            <a href="<?php echo iswPreviewImageUrl($dupAudioFile); ?>" target="_blank" rel="noopener noreferrer"><?php echo iswEscHtml(basename($dupAudioFile)); ?></a>
                            <button type="button" class="btn btn-red btn-sm duplicateRemoveMediaBtn" data-target-input="oldAudioFile">Remove</button>
                        </div>
                    <?php } ?>
                </div>
                <div class="form-group">
                    <h2>Featured</h2>
                    <div class="action-box inside-shadow" style="padding: 10px">
                        <input type="checkbox" value="1" name='featurePost' <?php echo ($dupIsFeatured ? 'checked' : ''); ?>> Mark as feature
                    </div>
                </div>
                <h2>Categories</h2>
                <div class="action-box height-200 inside-shadow">
                    <?php
                    $i = 0;
                    if ($categoryList = mysqli_query($connection, "SELECT category_id, category_title, link_url FROM " . TABLE_PREFIX . "category, " . TABLE_PREFIX . "link, " . TABLE_PREFIX . "meta WHERE category_site_id='" . SITE_ID . "' AND meta_relation_id=link_id AND link_type='category' AND link_relation_id=category_id AND link_status=200 AND category_status=1 AND category_type='" . rtrim(ltrim(ADMIN_BASE_URL, '/'), '/') . "' AND link_site_id=" . SITE_ID)) {
                        while ($category = mysqli_fetch_assoc($categoryList)) {
                            $catChecked = ($dupData !== null) ? (((int)$category['category_id'] === $selectedCategoryId) ? 'checked' : '') : (($i == 1) ? 'checked' : '');
                            ?>
                            <div class="form-group">
                                <input type="radio" name="category"
                                    value="<?php echo $category['category_id'] . '_' . str_replace('/category', '', $category['link_url']); ?>"
                                    <?php echo $catChecked; ?>>
                                <?php echo ucwords($category['category_title']); ?>
                            </div>
                    <?php
                            $i++;
                        }
                    }
                    ?>
                </div>
                <h2>Article type</h2>
                <div class="action-box height-200 inside-shadow">
                    <div class="form-group">
                        <input type="radio" name="paid" value="1" <?php echo (($dupPaid === 1) ? 'checked' : ''); ?>> Paid
                    </div>
                    <div class="form-group">
                        <input type="radio" name="paid" value="0" <?php echo (($dupPaid !== 1) ? 'checked' : ''); ?>> Free
                    </div>
                </div>
                <h2>News price</h2>
                <div class="action-box inside-shadow">
                    <div class="form-group">
                        <input type="number" onwheel="this.blur()" name="amount" class='form-control inside-shadow'
                            value="<?php echo iswEscHtml($dupAmount); ?>" placeholder="99" />
                    </div>
                </div>
                <h2>Tags</h2>
                <div class=" action-box inside-shadow">
                    <div class="form-group">
                        <input type="text" class='tags' />
                        <input type="hidden" id="tagValue" value="<?php echo iswEscHtml($dupTagsCsv); ?>">
                        <input type="hidden" id='tags' name="tags" value=''>
                    </div>
                </div>
                <div class="tagList" style="display: none;"></div>
                <h2>Authors</h2>
                <div class="action-box height-200 inside-shadow">
                    <div id="author-sort-list">
                        <?php
                        $allAuthors = array();
                        if ($authorList = mysqli_query($connection, "SELECT * FROM " . TABLE_PREFIX . "user INNER JOIN " . TABLE_PREFIX . "profile ON profile_user_id=user_id INNER JOIN " . TABLE_PREFIX . "site_access ON site_access_user_id=user_id WHERE site_access_type NOT IN ('subscriber', 'customer') AND site_access_site_id=" . SITE_ID . " ORDER BY profile_display_name ASC")) {
                            while ($author = mysqli_fetch_assoc($authorList)) {
                                $allAuthors[(int)$author['user_id']] = $author;
                            }
                        }
                        $sortedAuthors = array();
                        if (!empty($dupAuthors)) {
                            foreach ($dupAuthors as $dupUid => $dupSeq) {
                                if (isset($allAuthors[$dupUid])) {
                                    $sortedAuthors[] = $allAuthors[$dupUid];
                                    unset($allAuthors[$dupUid]);
                                }
                            }
                        }
                        foreach ($allAuthors as $author) {
                            $sortedAuthors[] = $author;
                        }
                        $authorIndex = 1;
                        foreach ($sortedAuthors as $author) {
                            $uid = (int)$author['user_id'];
                            $isAuthorChecked = isset($dupAuthors[$uid]);
                            $authorOrderVal = $isAuthorChecked ? (int)$dupAuthors[$uid] : '';
                            ?>
                            <div class="form-group author-item author-row" data-index="<?php echo $authorIndex; ?>">
                                <div class="author-left">
                                    <input type="checkbox" name="authorId[]" value="<?php echo $uid; ?>"
                                        id="author_<?php echo $uid; ?>" <?php echo ($isAuthorChecked ? 'checked' : ''); ?>>
                                    <label for="author_<?php echo $uid; ?>"
                                        class="author-label"><?php echo ucwords($author['profile_display_name']); ?></label>
                                </div>
                                <div class="author-right">
                                    <input type="number" class="author-order-input" name="authorOrder[]" value="<?php echo iswEscHtml($authorOrderVal); ?>" min="1"
                                        <?php echo ($isAuthorChecked ? '' : 'disabled'); ?>>
                                </div>
                            </div>
                        <?php $authorIndex++;
                        }
                        ?>
                    </div>
                </div>
                <style>
                    .author-row {
                        display: flex;
                        align-items: center;
                        justify-content: space-between;
                        padding: 6px 10px;
                        border-radius: 4px;
                        margin-bottom: 4px;
                        transition: background 0.2s;
                    }

                    .author-row:hover {
                        background: #f5f7fa;
                    }

                    .author-left {
                        display: flex;
                        align-items: center;
                        gap: 8px;
                    }

                    .author-label {
                        font-weight: 500;
                        color: #222;
                        margin-bottom: 0;
                    }

                    .author-right {
                        min-width: 60px;
                        text-align: right;
                    }

                    .author-order-input {
                        width: 48px;
                        text-align: right;
                        border-radius: 3px;
                        border: 1px solid #ccc;
                        padding: 2px 6px;
                        background: #fff;
                    }

                    @media (max-width: 600px) {
                        .author-row {
                            flex-direction: column;
                            align-items: flex-start;
                        }

                        .author-right {
                            text-align: left;
                            margin-top: 4px;
                        }
                    }
                </style>
                <script>
                    // Author order logic with validation and default selection
                    document.addEventListener('DOMContentLoaded', function() {
                        // Author order logic
                        const list = document.getElementById('author-sort-list');
                        if (!list) return;
                        const getCheckedItems = () => Array.from(list.querySelectorAll('.author-item')).filter(
                            item => item.querySelector('input[type="checkbox"]').checked);
                        const getOrderInputs = () => Array.from(list.querySelectorAll('.author-order-input'));
                        // Select first author by default when none are pre-selected
                        const firstAuthor = list.querySelector('.author-item input[type="checkbox"]');
                        if (getCheckedItems().length === 0 && firstAuthor) {
                            firstAuthor.checked = true;
                        }
                        // Update order inputs and validate
                        function updateOrderInputs() {
                            const checked = getCheckedItems();
                            // Assign order: use existing values if valid, else assign next available
                            let usedOrders = new Set();
                            checked.forEach((item, idx) => {
                                const orderInput = item.querySelector('.author-order-input');
                                let val = parseInt(orderInput.value);
                                if (!val || usedOrders.has(val)) {
                                    // Assign next available
                                    let next = 1;
                                    while (usedOrders.has(next)) next++;
                                    val = next;
                                }
                                orderInput.value = val;
                                orderInput.disabled = false;
                                usedOrders.add(val);
                            });
                            // Unchecked authors: clear and disable order
                            Array.from(list.querySelectorAll('.author-item')).forEach(item => {
                                if (!item.querySelector('input[type="checkbox"]').checked) {
                                    const orderInput = item.querySelector('.author-order-input');
                                    orderInput.value = '';
                                    orderInput.disabled = true;
                                }
                            });
                            // Check for duplicate order numbers
                            const orders = checked.map(item => parseInt(item.querySelector('.author-order-input')
                                .value));
                            const hasDup = orders.some((v, i, arr) => arr.indexOf(v) !== i);
                            checked.forEach(item => {
                                const orderInput = item.querySelector('.author-order-input');
                                if (hasDup) {
                                    orderInput.style.borderColor = 'red';
                                    orderInput.title = 'Duplicate order!';
                                } else {
                                    orderInput.style.borderColor = '#ccc';
                                    orderInput.title = '';
                                }
                            });
                        }
                        // Initial state
                        updateOrderInputs();
                        list.addEventListener('change', function(e) {
                            if (e.target.type === 'checkbox') {
                                updateOrderInputs();
                            }
                        });
                        // Allow manual editing of order input
                        list.addEventListener('input', function(e) {
                            if (e.target.classList.contains('author-order-input')) {
                                // Only allow positive integers
                                let v = e.target.value.replace(/[^0-9]/g, '');
                                e.target.value = v;
                                updateOrderInputs();
                            }
                        });
                    });
                </script>
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const audioFileInput = document.getElementById('audioFile');
                        const audioFileTrigger = document.getElementById('audioFileTrigger');
                        const audioFileName = document.getElementById('audioFileName');
                        const oldAudioInput = document.querySelector('input[name="oldAudioFile"]');

                        if (!audioFileInput || !audioFileTrigger || !audioFileName) {
                            return;
                        }

                        const updateAudioFileState = function() {
                            const selectedFile = audioFileInput.files && audioFileInput.files[0] ? audioFileInput.files[0] : null;

                            if (selectedFile) {
                                audioFileName.textContent = selectedFile.name;
                                audioFileName.style.display = 'block';
                                audioFileTrigger.textContent = 'Change Audio File';
                                return;
                            }

                            if (oldAudioInput && oldAudioInput.value.trim() !== '') {
                                audioFileName.textContent = '';
                                audioFileName.style.display = 'none';
                                audioFileTrigger.textContent = 'Change Audio File';
                                return;
                            }

                            audioFileName.textContent = '';
                            audioFileName.style.display = 'none';
                            audioFileTrigger.textContent = 'Select Audio File';
                        };

                        audioFileTrigger.addEventListener('click', function() {
                            audioFileInput.click();
                        });

                        audioFileTrigger.addEventListener('keydown', function(event) {
                            if (event.key === 'Enter' || event.key === ' ') {
                                event.preventDefault();
                                audioFileInput.click();
                            }
                        });

                        audioFileInput.addEventListener('change', updateAudioFileState);

                        document.addEventListener('click', function(event) {
                            if (event.target.closest('.duplicateRemoveMediaBtn[data-target-input="oldAudioFile"]')) {
                                setTimeout(updateAudioFileState, 0);
                            }
                        });

                        updateAudioFileState();
                    });
                </script>
                <h2>Feature Image</h2>
                <div class="form-group featureImageUploadBox upload-btn" style="display: <?php echo (($dupFeatureImg != '') ? 'none' : 'block'); ?>;">
                    <span>Set Feature Image</span>
                    <input type="hidden" name="oldFeatureImage" value="<?php echo iswEscHtml($dupFeatureImg); ?>">
                    <input type="hidden" name="oldThumbnailImage" value="<?php echo iswEscHtml($dupThumbImg); ?>">
                    <input type="file" id="featureImage" name="featureImage"
                        accept="image/x-png, image/gif, image/jpeg">
                    <p><strong>Only these file formats are supported:</strong> .png, .jpg, .jpeg, .gif
                        <strong>Recommended
                            file size maximum 500kb</strong>
                    </p>
                </div>
                <div class="img-preview featureImagePreviewBox action-box" style="display: <?php echo (($dupFeatureImg != '') ? 'block' : 'none'); ?>;">
                    <img src="<?php echo (($dupFeatureImg != '') ? iswPreviewImageUrl($dupFeatureImg) : '#'); ?>" id="featureImagePath" alt="">
                    <p id="featureChangeImage" class="btn btn-red btn-sm">Remove feature image</p>
                </div>
            </div>
        </form>
    </div>
</div>