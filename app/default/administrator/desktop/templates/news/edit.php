<?php

$news_id = base64_decode(mysqli_real_escape_string($connection, $_GET['id']));

if ($postDetails = mysqli_query($connection, "SELECT * FROM " . TABLE_PREFIX . "news, " . TABLE_PREFIX . "link, " . TABLE_PREFIX . "meta WHERE link_type='news' AND link_relation_id=news_id AND meta_relation_id=link_id AND news_id='{$news_id}' AND link_status!=301 AND link_type='news' AND link_site_id=" . SITE_ID . " LIMIT 1")) {
    if (mysqli_num_rows($postDetails) != 0) {
        extract(mysqli_fetch_assoc($postDetails));
    } else {
?>
        <script type="text/javascript">
            alert('Invalid action found');
            window.location.href = 'dashboard/news/?type=list&status=all';
        </script>
<?php
    }
}

?>
<div class="product-add-ui">
    <?php
    $isScheduledNews = ((int) $news_status === 2);
    $isDraftNews = (!$isScheduledNews && (((int) $news_status === 0) || $link_status == '206'));
    ?>
    <h2>Update
        <?php
        $newsLabelPrefix = '';
        if ($isScheduledNews) {
            $newsLabelPrefix = 'Scheduled ';
        } elseif ($isDraftNews) {
            $newsLabelPrefix = 'Drafted ';
        }
        echo $newsLabelPrefix . ucwords(rtrim(ltrim(ADMIN_BASE_URL, '/'), '/'));
        ?>
        <span><a href="<?php echo $postType ?>/?type=add&editor=true" class='btn btn-blue btn-sm'>Add New</a></span>
    </h2>

    <div class="form-box">
        <form id="formData" enctype="multipart/form-data">
            <div class="left-box">
                <div class="form-group">
                    <input type="text" class="form-control inside-shadow" name="title" placeholder="Title"
                        value="<?php echo $news_title ?>" required="">
                </div>
                <div class="form-group">
                    <div class="url">
                        <?php
                        $url = explode('/', ltrim(rtrim($link_url, '/'), '/'));
                        ?>
                        <p><strong>Permalink</strong></p>
                        <p><span><?php echo SITE_DOMAIN . '/...'; ?>/</span></p>
                        <input type="text" id="url" class="form-control" value="<?php echo end($url); ?>" name="url">
                        <input type="hidden" name='oldURL' value='<?php echo $link_url ?>'>
                    </div>
                </div>
                <div class="form-group fullscreen-editor">
                    <textarea name="description"
                        class="editor inside-shadow"><?php echo html_entity_decode($news_description); ?></textarea>
                </div>

                <?php

                if (isset($theme)) {
                    if ($theme == 'gallery') { ?>


                        <!-- gallery description and other things -->

                        <section style="margin-bottom:20px">
                            <h3>Manage images</h3>
                            <div class="productGallery generalTab">
                                <div class="input-field">
                                    <div class="input-images" style="padding-top: .5rem;"></div>
                                </div>
                                <?php
                                if ($news_gallery != '') {

                                    $imageList = json_decode($news_gallery, TRUE);

                                    $oldGalleryImages = array();

                                    for ($i = 0; $i < sizeof($imageList); $i++) {

                                        $oldGalleryImages[] = array(
                                            'id' => $i + 1,
                                            'name' => $imageList[$i]['name'],
                                            'src' => $imageList[$i]['path'],
                                            'type' => $imageList[$i]['type'],
                                        );
                                    }

                                    $oldGalleryImages = json_encode($oldGalleryImages, TRUE);
                                } else {

                                    $oldGalleryImages = json_encode('', TRUE);
                                }

                                echo "<script> var oldGalleryImages = {$oldGalleryImages}</script>";

                                ?>
                            </div>

                        </section>
                <?php
                    }
                }

                ?>

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
                            <input type="text" name="meta_title" value="<?php echo $meta_title; ?>" id="meta_title"
                                class="form-control inside-shadow" placeholder="Meta title">
                        </div>
                        <div class="form-group">
                            <label>Meta Description</label>
                            <textarea name="meta_desc" id="meta_desc" class="form-control inside-shadow"
                                placeholder="Meta Description"><?php echo $meta_description ?></textarea>
                        </div>
                        <div class="form-group">
                            <label>Meta Keywords</label>
                            <textarea name="meta_key" id="meta_keywords" class="form-control inside-shadow"
                                placeholder="Enter keywords of the news ( use , to seprate keywords )"><?php echo $meta_keyword ?></textarea>
                        </div>
                        <div class="form-group">
                            <label>Canonical URL</label>
                            <input type="text" name="canonical" class="form-control inside-shadow"
                                value="<?php echo $meta_canonical; ?>" id="canonical"
                                placeholder="Actual URL of the <?php echo $postType ?>">
                        </div>
                        <div class="form-group">
                            <label>Allow search engines to show this news in search results?</label>
                            <select name="robots" class="form-control inside-shadow">
                                <option value="index, follow"
                                    <?php echo (($meta_robots == 'index, follow') ? 'selected' : '') ?>>Yes</option>
                                <option value="noindex, nofollow"
                                    <?php echo (($meta_robots == 'noindex, nofollow') ? 'selected' : '') ?>>No</option>
                            </select>
                        </div>
                    </div>
                    <div class="fb generalTab" style="display: none;">
                        <div class="facebookPreviewBox"
                            style="display: <?php echo (($fb_img == '') ? 'none' : 'block') ?>;">
                            <input type="hidden" name="oldFacebookImage" value="<?php echo $fb_img ?>">
                            <div class="img"><img src="<?php echo $fb_img; ?>" id="facebookPath" alt=""></div>
                            <p id="facebookChangeImage" class='changeImage'>Remove facebook feature image</p>
                        </div>
                        <div class="facebookUploadBox"
                            style="display: <?php echo (($fb_img == '') ? 'block' : 'none') ?>;">
                            <span>Set facebook feature image</span>
                            <input type='file' id="facebookImage" name='facebookImage' />
                        </div>
                        <div class="form-group">
                            <label>Facebook Title</label>
                            <input type="text" name="fb_title" class="form-control inside-shadow"
                                value="<?php echo $fb_title ?>" placeholder="Facebook title">
                        </div>
                        <div class="form-group">
                            <label>Facebook Description</label>
                            <textarea name="fb_desc" id="fb_desc" class="form-control inside-shadow"
                                placeholder="Facebook Description"><?php echo $fb_desc ?></textarea>
                        </div>
                    </div>
                    <div class="tw generalTab" style="display: none;">
                        <div class="twitterPreviewBox"
                            style="display: <?php echo (($tw_img == '') ? 'none' : 'block') ?>;">
                            <input type="hidden" name="oldTwitterImage" value="<?php echo $tw_img ?>">
                            <div class="img"><img src="<?php echo $tw_img ?>" id="twitterPath" alt=""></div>
                            <p id="twitterChangeImage" class="changeImage">Remove twitter feature image</p>
                        </div>
                        <div class="twitterUploadBox"
                            style="display: <?php echo (($fb_img == '') ? 'block' : 'none') ?>;">
                            <span>Set Twitter feature image</span>
                            <input type='file' id="twitterImage" name='twitterImage' />
                        </div>
                        <div class="form-group">
                            <label>Twitter Title</label>
                            <input type="text" name="tw_title" class="form-control inside-shadow"
                                value="<?php echo $tw_title; ?>" placeholder="Twitter title">
                        </div>
                        <div class="form-group">
                            <label>Twitter Description</label>
                            <textarea name="tw_desc" id="tw_desc" class="form-control inside-shadow"
                                placeholder="Twitter Description"><?php echo $tw_desc ?></textarea>
                        </div>
                    </div>
                </section>
            </div>
            <div class="right-box">
                <div class="action-box inside-shadow">
                    <?php if ($isDraftNews) { ?>
                        <input type="button" id="updatePost" class="btn btn-green btn-block" value="Publish news">
                        <input type="button" id="draftPost" class="btn btn-yellow btn-block" value="Update Draft">
                    <?php } else { ?>
                        <input type="button" id="updatePost" class="btn btn-green btn-block" value="Update news">
                    <?php } ?>
                    <?php if ($isScheduledNews) { ?>
                        <div class="form-group schedule-post-box">
                            <label>Schedule publish date &amp; time</label>
                            <input type="datetime-local" id="scheduleAt" name="schedule_at"
                                class="form-control inside-shadow" placeholder="Select date and time (YYYY-MM-DD HH:MM)"
                                title="Select date and time" min="<?php echo date('Y-m-d\\TH:i'); ?>" autocomplete="off"
                                inputmode="none"
                                value="<?php echo (($news_status == 2 && !empty($link_published)) ? date('Y-m-d\TH:i', strtotime($link_published)) : ''); ?>">
                            <small class="schedule-help">Select date and time (YYYY-MM-DD HH:MM)</small>
                        </div>
                        <input type="button" id="schedulePost" class="btn btn-blue btn-block" value="Update Schedule">
                    <?php } ?>
                    <input type="hidden" name="type" value="<?php echo rtrim(ltrim(ADMIN_BASE_URL, '/'), '/'); ?>">
                    <input type="hidden" name="newsType" value="<?php echo ((isset($theme)) ? $theme : 'news') ?>">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name='postStatus' value='<?php echo $news_status ?>'>
                    <input type="hidden" name="id" value="<?php echo $news_id ?>">
                    <input type="hidden" name="postType" value="<?php echo $news_type ?>">
                    <a href='<?php echo SITE_URL . $link_url; ?>' class='btn btn-block btn-blue' target='_blank'>View
                        live
                        news</a>
                </div>
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const audioFileInput = document.getElementById('audioFile');
                        const audioFileTrigger = document.getElementById('audioFileTrigger');
                        const audioFileName = document.getElementById('audioFileName');

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

                        updateAudioFileState();
                    });
                </script>
                <div class="form-group inside-shadow">
                    <h2>Audio File</h2>
                    <div class="form-group audioFileUploadBox upload-btn">
                        <span id="audioFileTrigger" role="button" tabindex="0">Select Audio File</span>
                        <input type="file" id="audioFile" name="audioFile" style="display: none;"
                            accept="audio/mpeg,audio/mp3,audio/wav,audio/x-wav,audio/mp4,audio/x-m4a,audio/aac,audio/ogg,audio/webm">
                        <p id="audioFileName" style="margin-top: 20px; display: none;"></p>
                    </div>
                    <input type="hidden" name="oldAudioFile" value="<?php echo $news_audio; ?>">
                    <p style=" margin-top: 20px;float: left;font-size: 14px;"><?php echo (($news_audio != '') ? "<a href='$news_audio' style='color: #333 !important' target='_blank' rel='noopener noreferrer'>Listen to audio <i class='fas fa-external-link-alt' style='margin-left: 5px;'></i></a>" : 'No audio file selected'); ?></p>
                </div>
                <div class="form-group">
                    <h2>Featured</h2>
                    <div class="action-box inside-shadow" style="padding: 10px">
                        <input type="checkbox" value="1" name='featurePost'
                            <?php echo (($link_is_featured == '1') ? 'checked' : '') ?>> Mark as Feature news
                    </div>
                </div>
                <h2>Categories</h2>
                <div class="action-box height-200 inside-shadow">
                    <?php
                    if ($categoryList = mysqli_query($connection, "SELECT category_id, category_title, link_url FROM " . TABLE_PREFIX . "category, " . TABLE_PREFIX . "link, " . TABLE_PREFIX . "meta WHERE category_site_id='" . SITE_ID . "' AND meta_relation_id=link_id AND link_type='category' AND link_relation_id=category_id AND link_status=200 AND category_status=1 AND category_type='" . rtrim(ltrim(ADMIN_BASE_URL, '/'), '/') . "' AND link_site_id=" . SITE_ID)) {
                        while ($category = mysqli_fetch_assoc($categoryList)) {
                            if ($category['category_id'] == ${$postType . '_category'}) { ?>
                                <div class="form-group">
                                    <input type="radio" name="category"
                                        value="<?php echo $category['category_id'] . '_' . str_replace('/category', '', $category['link_url']); ?>"
                                        <?php echo (($category['category_id'] == $news_category) ? 'checked' : '') ?>>
                                    <?php echo ucwords($category['category_title']); ?>
                                </div>
                            <?php
                            } else { ?>
                                <div class="form-group">
                                    <input type="radio" name="category"
                                        value="<?php echo $category['category_id'] . '_' . str_replace('/category', '', $category['link_url']); ?>">
                                    <?php echo ucwords($category['category_title']); ?>
                                </div>
                    <?php
                            }
                        }
                    }
                    ?>
                </div>
                <h2>Article type</h2>
                <div class="action-box height-200 inside-shadow">
                    <div class="form-group">
                        <input type="radio" name="paid" value="1" <?php echo (($news_paid == 1) ? 'checked' : '') ?>>
                        Paid
                    </div>
                    <div class="form-group">
                        <input type="radio" name="paid" value="0" <?php echo (($news_paid == 0) ? 'checked' : '') ?>>
                        Free
                    </div>
                </div>
                <h2>News price</h2>
                <div class="action-box inside-shadow">
                    <div class="form-group">
                        <input type="number" onwheel="this.blur()" name="amount" class='form-control inside-shadow'
                            value="<?php echo $news_price ?>" />
                    </div>
                </div>
                <h2>Tags</h2>
                <?php
                $tagpostID = (($news_parent_id == 0) ? $news_id : $news_parent_id);

                if ($tagList = mysqli_query($connection, "SELECT tag_title FROM " . TABLE_PREFIX . "tag WHERE tag_relation_id='{$tagpostID}' AND tag_site_id='" . SITE_ID . "' AND tag_type='news'")) {
                    if (mysqli_num_rows($tagList) != 0) {
                        while ($tagsName = mysqli_fetch_assoc($tagList)) {
                            $tags[] = $tagsName['tag_title'];
                        }
                        $tags = implode(',', $tags);
                    } else {
                        $tags = '';
                    }
                }
                ?>
                <div class="action-box inside-shadow">
                    <div class="form-group">
                        <input type="text" class='tags' />
                        <input type="hidden" id="tagValue" value="<?php echo $tags; ?>">
                        <input type="hidden" id='tags' name="tags" value=''>
                    </div>
                </div>
                <div class="tagList" style="display: none;"></div>

                <h2>Authors</h2>
                <div class="action-box height-200 inside-shadow">
                    <div id="author-sort-list">
                        <?php
                        // Fetch all authors and their order for this news
                        $selectedAuthors = array();
                        $authorOrder = array();
                        // Fetch authors ordered by seq_no
                        $authorQuery = mysqli_query($connection, "SELECT profile_user_id, seq_no FROM " . TABLE_PREFIX . "authors WHERE rel_id='{$news_id}' AND rel_type='news' ORDER BY seq_no ASC");
                        if ($authorQuery && mysqli_num_rows($authorQuery) > 0) {
                            while ($row = mysqli_fetch_assoc($authorQuery)) {
                                $selectedAuthors[] = $row['profile_user_id'];
                                $authorOrder[$row['profile_user_id']] = $row['seq_no'];
                            }
                        }
                        // Get all possible authors
                        $allAuthors = array();
                        if ($authorList = mysqli_query($connection, "SELECT * FROM " . TABLE_PREFIX . "user INNER JOIN " . TABLE_PREFIX . "profile ON profile_user_id=user_id INNER JOIN " . TABLE_PREFIX . "site_access ON site_access_user_id=user_id WHERE site_access_type NOT IN ('subscriber', 'customer') AND site_access_site_id=" . SITE_ID . " ORDER BY profile_display_name ASC")) {
                            while ($author = mysqli_fetch_assoc($authorList)) {
                                $allAuthors[$author['user_id']] = $author;
                            }
                        }
                        // Sort selected authors first by their order, then unselected
                        $sortedAuthors = array();
                        foreach ($selectedAuthors as $uid) {
                            if (isset($allAuthors[$uid])) {
                                $sortedAuthors[] = $allAuthors[$uid];
                                unset($allAuthors[$uid]);
                            }
                        }
                        foreach ($allAuthors as $author) {
                            $sortedAuthors[] = $author;
                        }
                        $authorIndex = 1;
                        foreach ($sortedAuthors as $author) {
                            $uid = $author['user_id'];
                            $checked = in_array($uid, $selectedAuthors) ? 'checked' : '';
                            $orderVal = isset($authorOrder[$uid]) ? $authorOrder[$uid] : '';
                        ?>
                            <div class="form-group author-item author-row" data-index="<?php echo $authorIndex; ?>">
                                <div class="author-left">
                                    <input type="checkbox" name="authorId[]" value="<?php echo $uid ?>"
                                        id="author_<?php echo $uid ?>" <?php echo $checked; ?>>
                                    <label for="author_<?php echo $uid ?>"
                                        class="author-label"><?php echo ucwords($author['profile_display_name']); ?></label>
                                </div>
                                <div class="author-right">
                                    <input type="number" class="author-order-input" name="authorOrder[]"
                                        value="<?php echo $orderVal; ?>" min="1"
                                        <?php echo ($checked ? '' : 'disabled'); ?>>
                                </div>
                            </div>
                        <?php $authorIndex++;
                        } ?>
                    </div>
                </div>
                <style>
                    .author-row {
                        display: flex;
                        align-items: center;
                        justify-content: space-between;
                        padding: 3px 6px;
                        border-radius: 3px;
                        border-bottom: 1px solid #ececec;
                        margin-bottom: 0;
                        font-size: 15px;
                        min-height: 32px;
                        transition: background 0.18s;
                    }

                    .author-row:last-child {
                        border-bottom: none;
                    }

                    .author-row:hover {
                        background: #f7fafd;
                    }

                    .author-left {
                        display: flex;
                        align-items: center;
                        gap: 7px;
                    }

                    .author-label {
                        font-weight: 500;
                        color: #222;
                        margin-bottom: 0;
                        font-size: 15px;
                    }

                    .author-right {
                        min-width: 48px;
                        text-align: right;
                    }

                    .author-order-input {
                        width: 40px;
                        text-align: right;
                        border-radius: 3px;
                        border: 1px solid #ccc;
                        padding: 2px 4px;
                        background: #fff;
                        font-size: 15px;
                    }

                    @media (max-width: 600px) {
                        .author-row {
                            flex-direction: column;
                            align-items: flex-start;
                            min-height: unset;
                        }

                        .author-right {
                            text-align: left;
                            margin-top: 3px;
                        }
                    }
                </style>
                <script>
                    // Improved author order input: allow direct editing, validate for duplicates and positive integers
                    // and keep order unique among checked authors

                    document.addEventListener('DOMContentLoaded', function() {
                        const list = document.getElementById('author-sort-list');
                        if (!list) return;
                        const getCheckedItems = () => Array.from(list.querySelectorAll('.author-item')).filter(
                            item => item.querySelector('input[type="checkbox"]').checked);
                        // Update order inputs and validate
                        function updateOrderInputs() {
                            const checked = getCheckedItems();
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
                <h2>Feature Image</h2>
                <div class="form-group featureImageUploadBox upload-btn"
                    style="display: <?php echo (($news_image != '') ? 'none' : 'block') ?>;">
                    <span>Set Feature Image</span>
                    <input type="hidden" name="oldFeatureImage" value="<?php echo $news_image ?>">
                    <input type="file" id="featureImage" name="featureImage"
                        accept="image/x-png, image/gif, image/jpeg">
                    <p><strong>Only these file formats are supported:</strong> .png, .jpg, .jpeg, .gif<strong>Maximum
                            upload
                            limit is 300 MB</strong></p>
                </div>
                <div class="img-preview featureImagePreviewBox action-box"
                    style="display: <?php echo (($news_image != '') ? 'block' : 'none') ?>;">
                    <img src="//<?php echo SITE_DOMAIN . $news_image ?>" id="featureImagePath" alt="">
                    <p id="featureChangeImage" class="btn btn-red btn-sm">Remove feature image</p>
                </div>
            </div>
        </form>
    </div>
</div>