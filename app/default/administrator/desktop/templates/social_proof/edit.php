<style type="text/css">
    /* Reset generic .form-group span styles for select2 elements */
    .form-group span.select2-container,
    .form-group span.select2-container span,
    .select2-container span,
    span.select2-dropdown span {
        background: transparent !important;
        padding: 0 !important;
        border-radius: 0 !important;
        color: inherit !important;
        box-shadow: none !important;
        float: none !important;
        width: auto !important;
        display: inline !important;
    }

    /* Ensure the outer container is styled properly and not broken by generic span styling */
    .form-group span.select2-container {
        background: transparent !important;
        padding: 0 !important;
        border-radius: 0 !important;
        color: inherit !important;
        box-shadow: none !important;
        display: inline-block !important;
        float: left !important;
        width: 100% !important;
    }

    /* Style the selection box itself */
    .form-group span.select2-container span.select2-selection--single {
        background-color: #fff !important;
        border: none !important;
        border-radius: 4px !important;
        height: 32px !important;
        display: flex !important;
        align-items: center !important;
        padding: 0 !important;
        box-shadow: inset 0 0 2px 0 #9c9c9c !important;
        /* To match .inside-shadow inputs */
        width: 100% !important;
        box-sizing: border-box !important;
        position: relative !important;
        float: left !important;
    }

    /* Style the rendered text */
    .form-group span.select2-container span.select2-selection--single span.select2-selection__rendered {
        color: #333 !important;
        line-height: 32px !important;
        padding-left: 10px !important;
        padding-right: 25px !important;
        text-align: left !important;
        width: 100% !important;
        box-sizing: border-box !important;
        display: block !important;
        font-size: 14px !important;
    }

    /* Style the placeholder */
    .form-group span.select2-container span.select2-selection--single span.select2-selection__placeholder {
        color: #999 !important;
    }

    /* Style the arrow container and icon */
    .form-group span.select2-container span.select2-selection--single span.select2-selection__arrow {
        height: 32px !important;
        position: absolute !important;
        top: 0 !important;
        right: 0 !important;
        width: 20px !important;
        background: transparent !important;
        display: block !important;
    }

    /* Reset any b elements inside the arrow if they get styled */
    .form-group span.select2-container span.select2-selection--single span.select2-selection__arrow b {
        border-color: #888 transparent transparent transparent !important;
        border-style: solid !important;
        border-width: 5px 4px 0 4px !important;
        height: 0 !important;
        left: 50% !important;
        margin-left: -4px !important;
        margin-top: -2px !important;
        position: absolute !important;
        top: 50% !important;
        width: 0 !important;
    }

    /* When select2 is open, arrow points up */
    .form-group span.select2-container--open span.select2-selection--single span.select2-selection__arrow b {
        border-color: transparent transparent #888 transparent !important;
        border-width: 0 4px 5px 4px !important;
    }

    /* Select2 Dropdown overrides */
    .select2-container--open .select2-dropdown {
        border: 1px solid #ccc !important;
        border-radius: 4px !important;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15) !important;
        background-color: #fff !important;
        z-index: 99999 !important;
        width: 100% !important;
    }

    /* Override all nested spans and list items in dropdown to force white background and normal colors */
    .select2-dropdown span,
    .select2-dropdown li,
    .select2-dropdown ul,
    .select2-dropdown div {
        background-color: #fff !important;
        background: #fff !important;
        color: #333 !important;
        padding: 0 !important;
        border-radius: 0 !important;
        box-shadow: none !important;
        float: none !important;
    }

    /* Dropdown search container reset */
    .select2-dropdown .select2-search--dropdown {
        padding: 4px !important;
        background-color: #fff !important;
        display: block !important;
    }

    /* Dropdown search input field */
    .select2-dropdown .select2-search--dropdown .select2-search__field {
        border: 1px solid #ccc !important;
        border-radius: 4px !important;
        padding: 5px 8px !important;
        box-sizing: border-box !important;
        width: 100% !important;
        background-color: #fff !important;
        color: #333 !important;
    }

    /* Results list items container */
    .select2-dropdown .select2-results__options {
        background-color: #fff !important;
        padding: 0 !important;
        margin: 0 !important;
        list-style: none !important;
    }

    /* List item options styling */
    .select2-dropdown .select2-results__option {
        padding: 6px 10px !important;
        background-color: #fff !important;
        color: #333 !important;
        float: none !important;
        width: 100% !important;
        box-sizing: border-box !important;
        display: list-item !important;
        text-align: left !important;
    }

    /* Highlighted option */
    .select2-container--default .select2-results__option--highlighted[aria-selected] {
        background-color: #00609e !important;
        color: #fff !important;
    }

    /* Selected option (non-highlighted) */
    .select2-container--default .select2-results__option[aria-selected=true] {
        background-color: #eef5fb !important;
        color: #333 !important;
    }
</style>
<?php

$social_proof_id = base64_decode(mysqli_real_escape_string($connection, $_GET['id']));

if ($postDetails = mysqli_query($connection, "SELECT * FROM " . TABLE_PREFIX . "social_proof, " . TABLE_PREFIX . "link, " . TABLE_PREFIX . "meta WHERE link_type='social_proof' AND link_relation_id=sp_id AND meta_relation_id=link_id AND sp_id='{$social_proof_id}' AND link_status!=301 AND link_type='social_proof' AND link_site_id=" . SITE_ID . " LIMIT 1")) {
    if (mysqli_num_rows($postDetails) != 0) {
        extract(mysqli_fetch_assoc($postDetails));
    } else { ?>
        <script type="text/javascript">
            alert('Invalid action found');
            window.location.href = 'dashboard/social_proof/?type=list&status=all';
        </script>
<?php
    }
}

?>
<div class="product-add-ui">
    <h2>Update <?php echo ucwords(str_replace('_', ' ', (($link_status == '206') ? 'Drafted ' : '') . ucwords(rtrim(ltrim(ADMIN_BASE_URL, '/'), '/')))); ?>
        <span><a href="<?php echo $postType ?>/?type=add&editor=true" class='btn btn-blue btn-sm'>Add New</a></span>
    </h2>

    <?php
    $video_url_extracted = '';
    if (isset($sp_video_url) && $sp_video_url != '') {
        $video_url_extracted = $sp_video_url;
    } else {
        $desc_decoded = html_entity_decode($sp_description);
        if (preg_match('/src="https:\/\/www\.youtube\.com\/embed\/([^"\?]+)/i', $desc_decoded, $match)) {
            $video_url_extracted = 'https://www.youtube.com/watch?v=' . $match[1];
        } elseif (preg_match('/data-instgrm-permalink="([^"]+)"/i', $desc_decoded, $match)) {
            $video_url_extracted = $match[1];
        } elseif (preg_match('/cite="([^"]+)"/i', $desc_decoded, $match)) {
            if (strpos($match[1], 'tiktok.com') !== false) {
                $video_url_extracted = $match[1];
            }
        }
    }
    ?>

    <div class="form-box">
        <form method="post" id="formData" enctype="multipart/form-data">
            <div class="left-box">
                <div class="form-group">
                    <input type="text" class="form-control inside-shadow" id="title" name="title" placeholder="Title"
                        value="<?php echo $sp_title ?>" required="">
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
                        class="editor inside-shadow"><?php echo html_entity_decode($sp_description); ?></textarea>
                </div>
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
                                placeholder="Enter keywords of the post ( use , to seprate keywords )"><?php echo $meta_keyword ?></textarea>
                        </div>
                        <div class="form-group">
                            <label>Canonical URL</label>
                            <input type="text" name="canonical" class="form-control inside-shadow"
                                value="<?php echo $meta_canonical; ?>" id="canonical"
                                placeholder="Actual URL of the <?php echo $postType ?>">
                        </div>
                        <div class="form-group">
                            <label>Allow search engines to show this post in search results?</label>
                            <select name="robots" class="form-control inside-shadow">
                                <option value="index, follow"
                                    <?php echo (($meta_robots == 'index, follow') ? 'selected' : '') ?>>Yes</option>
                                <option value="noindex, nofollow"
                                    <?php echo (($meta_robots == 'noindex, nofollow') ? 'selected' : '') ?>>No</option>
                            </select>
                        </div>
                    </div>
                    <div class="fb generalTab" style="display: none;">
                        <div class="facebookPreviewBox" style="display: <?php echo (($fb_img == '') ? 'none' : 'block') ?>;">
                            <input type="hidden" name="oldFacebookImage" value="<?php echo $fb_img ?>">
                            <div class="img"><img src="<?php echo $fb_img; ?>" id="facebookPath" alt=""></div>
                            <p id="facebookChangeImage" class='changeImage'>Remove facebook feature image</p>
                        </div>
                        <div class="facebookUploadBox" style="display: <?php echo (($fb_img == '') ? 'block' : 'none') ?>;">
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
                        <div class="twitterPreviewBox" style="display: <?php echo (($tw_img == '') ? 'none' : 'block') ?>;">
                            <input type="hidden" name="oldTwitterImage" value="<?php echo $tw_img ?>">
                            <div class="img"><img src="<?php echo $tw_img ?>" id="twitterPath" alt=""></div>
                            <p id="twitterChangeImage" class="changeImage">Remove twitter feature image</p>
                        </div>
                        <div class="twitterUploadBox" style="display: <?php echo (($fb_img == '') ? 'block' : 'none') ?>;">
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
                    <input type="button" id="updatePost" class="btn btn-green btn-block"
                        value=" <?php echo (($sp_status == 1) ? 'Update ' : 'Publish '); ?>post">
                    <!-- <input type="submit" id="draftPost" class="btn btn-yellow btn-block" value="Save Draft"> -->
                    <input type="hidden" name="type" value="<?php echo rtrim(ltrim(ADMIN_BASE_URL, '/'), '/'); ?>">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name='postStatus' value='<?php echo $sp_status ?>'>
                    <input type="hidden" name="id" value="<?php echo $social_proof_id ?>">
                    <input type="hidden" name="postType" value="<?php echo $postType ?>">
                    <a href='<?php echo SITE_URL . $link_url; ?>' class='btn btn-block btn-blue' target='_blank'>View live
                        post</a>
                </div>
                <div class="form-group">
                    <h2>Featured</h2>
                    <div class="action-box inside-shadow" style="padding: 10px">
                        <input type="checkbox" value="1" name='featurePost'
                            <?php echo (($link_is_featured == '1') ? 'checked' : '') ?>> Mark as feature
                    </div>
                </div>
                <h2>Categories</h2>
                <div class="action-box height-200 inside-shadow">
                    <?php
                    if ($categoryList = mysqli_query($connection, "SELECT category_id, category_title, link_url FROM " . TABLE_PREFIX . "category, " . TABLE_PREFIX . "link, " . TABLE_PREFIX . "meta WHERE category_site_id='" . SITE_ID . "' AND meta_relation_id=link_id AND link_type='category' AND link_relation_id=category_id AND link_status=200 AND category_status=1 AND category_type='social_proof' AND link_site_id=" . SITE_ID)) {
                        while ($category = mysqli_fetch_assoc($categoryList)) {
                            if ($category['category_id'] == ${'sp_category'}) { ?>
                                <div class="form-group">
                                    <input type="radio" name="category"
                                        value="<?php echo $category['category_id'] . '_' . str_replace('/category', '', $category['link_url']); ?>"
                                        <?php echo (($category['category_id'] == $sp_category) ? 'checked' : '') ?>>
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
                <h2>Tags</h2>
                <?php
                $tagpostID = (($sp_parent_id == 0) ? $social_proof_id : $sp_parent_id);

                if ($tagList = mysqli_query($connection, "SELECT tag_title FROM " . TABLE_PREFIX . "tag WHERE tag_relation_id='{$tagpostID}' AND tag_site_id='" . SITE_ID . "' AND tag_type='post'")) {
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
                <h2>Link to Product / Service / Page</h2>
                <div class="action-box inside-shadow" style="padding: 10px;">
                    <div class="form-group" style="margin-bottom: 0;">
                        <select name="link_url_ref" class="form-control inside-shadow" id="link_url_ref" style="width: 100%;">
                            <option value="">-- Select None --</option>
                            <?php
                            $queryLinkable = "
                                SELECT 'Product' AS item_type, product_title AS title, link_relation_id, link_type FROM " . TABLE_PREFIX . "product JOIN " . TABLE_PREFIX . "link ON link_relation_id=product_id WHERE link_type='product' AND product_status=1 AND link_status=200 AND product_site_id=" . SITE_ID . "
                                UNION ALL
                                SELECT 'Service' AS item_type, service_title AS title, link_relation_id, link_type FROM " . TABLE_PREFIX . "service JOIN " . TABLE_PREFIX . "link ON link_relation_id=service_id WHERE link_type='service' AND service_status=1 AND link_status=200 AND service_site_id=" . SITE_ID . "
                                UNION ALL
                                SELECT 'Page' AS item_type, page_title AS title, link_relation_id, link_type FROM " . TABLE_PREFIX . "page JOIN " . TABLE_PREFIX . "link ON link_relation_id=page_id WHERE link_type='page' AND page_status=1 AND link_status=200 AND page_site_id=" . SITE_ID . "
                                ORDER BY item_type, title ASC
                            ";
                            if ($linkableList = mysqli_query($connection, $queryLinkable)) {
                                while ($item = mysqli_fetch_assoc($linkableList)) {
                                    $selected = (isset($sp_link_url) && $sp_link_url == $item['link_url']) ? 'selected' : '';
                                    echo '<option value="' . htmlspecialchars($item['link_relation_id']) . '_' . htmlspecialchars($item['link_type']) . '" ' . (($item['link_relation_id'] == $sp_ref_id) ? 'selected' : '') . ' >';
                                    echo '[' . $item['item_type'] . '] ' . htmlspecialchars($item['title']);
                                    echo '</option>';
                                }
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <h2>Feature Image</h2>
                <?php
                $isRemoteImage = (strpos($sp_image, 'http://') === 0 || strpos($sp_image, 'https://') === 0);
                $hasLocalImage = ($sp_image != '' && !$isRemoteImage);
                $hasRemoteImage = ($sp_image != '' && $isRemoteImage);
                ?>
                <div class="form-group featureImageUploadBox upload-btn"
                    style="display: <?php echo (($sp_image != '') ? 'none' : 'block') ?>;">
                    <span>Set Feature Image</span>
                    <input type="hidden" name="oldFeatureImage" value="<?php echo $sp_image ?>">
                    <input type="file" id="featureImage" name="featureImage" accept="image/x-png, image/gif, image/jpeg">
                    <p><strong>Only these file formats are supported:</strong> .png, .jpg, .jpeg, .gif<strong>Maximum upload
                            limit is 300 MB</strong></p>
                </div>
                <div class="img-preview featureImagePreviewBox action-box"
                    style="display: <?php echo ($hasLocalImage ? 'block' : 'none') ?>;">
                    <img src="//<?php echo SITE_DOMAIN . ($hasLocalImage ? $sp_image : '') ?>" id="featureImagePath" alt="">
                    <p id="featureChangeImage" class="btn btn-red btn-sm">Remove feature image</p>
                </div>
                <div class="img-preview remoteImagePreviewBox action-box"
                    style="display: <?php echo ($hasRemoteImage ? 'block' : 'none') ?>; padding: 10px;">
                    <img src="<?php echo ($hasRemoteImage ? $sp_image : '') ?>" id="remoteImagePath" alt="" referrerpolicy="no-referrer" style="max-width: 100%; height: auto; display: block; margin-bottom: 10px; border-radius: 4px;">
                    <p id="remoteChangeImage" class="btn btn-red btn-sm">Remove remote image</p>
                </div>
                <input type="hidden" name="remote_image" id="remote_image" value="<?php echo ($hasRemoteImage ? htmlspecialchars($sp_image) : '') ?>">
            </div>
        </form>
    </div>

    <script type="text/javascript">
        document.addEventListener("DOMContentLoaded", function() {
            if (typeof jQuery === "undefined") return;
            var $ = jQuery;

            // Clean URL text helper (removes emojis and all special characters except letters, numbers, spaces, and hyphens)
            function cleanUrlText(text) {
                return text
                    .toString()
                    .toLowerCase()
                    .replace(/[\uE000-\uF8FF]|\uD83C[\uDC00-\uDFFF]|\uD83D[\uDC00-\uDFFF]|[\u2011-\u26FF]|\uD83E[\uDD10-\uDDFF]/g, "") // remove emojis
                    .replace(/[^a-z0-9\s-]/g, "") // remove all special characters except letters, numbers, spaces, and hyphens
                    .trim()
                    .replace(/\s+/g, "-") // replace spaces with hyphens
                    .replace(/-+/g, "-"); // collapse multiple hyphens
            }

            // Title to clean URL generator override
            $("#title").on("keyup keypress change", function() {
                setTimeout(function() {
                    var val = $("#title").val();
                    var cleanedUrl = cleanUrlText(val);
                    $("#url").val(cleanedUrl);
                }, 5);
            });

            $("#url").on("keyup blur change", function() {
                var val = $(this).val();
                var cleanedUrl = cleanUrlText(val);
                $(this).val(cleanedUrl);
            });

            // Initialize Select2 for Product/Service/Page selection
            function initSelect2() {
                var $select = $("#link_url_ref");
                $select.select2({
                    placeholder: "-- Select None --",
                    allowClear: true,
                    width: '100%',
                    dropdownParent: $select.parent()
                });
            }

            if (!$.fn.select2) {
                $("head").append('<link rel="stylesheet" href="<?php echo SITE_URL . '/' . PLUGIN_LIBRARY; ?>select2/css/select2.min.css">');
                $.getScript("<?php echo SITE_URL . '/' . PLUGIN_LIBRARY; ?>select2/js/select2.min.js", function() {
                    initSelect2();
                });
            } else {
                initSelect2();
            }

            // Handle URL fetch
            $('#btn_fetch_video').on('click', function(e) {
                e.preventDefault();
                var videoUrl = $.trim($('#video_url').val());
                if (!videoUrl) {
                    alert('Please enter a video URL first.');
                    return;
                }

                // Show loading state
                var $btn = $(this);
                var origText = $btn.text();
                $btn.text('Fetching...').prop('disabled', true);

                $.ajax({
                    url: '/api/social_proof/meta/',
                    type: 'GET',
                    data: {
                        url: videoUrl
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response && response.status == 200) {
                            if (response.title) {
                                $('#title').val(response.title).trigger('keyup');
                            }
                            if (response.thumbnail) {
                                setRemoteThumbnail(response.thumbnail);
                            }
                            if (response.embed) {
                                var html = response.embed;
                                if (response.description && response.description !== response.title && response.description !== 'Instagram Reel' && response.description !== 'Instagram Reel') {
                                    html += '<p>' + response.description + '</p>';
                                }
                                setEditorContent(html);
                            }
                        } else {
                            alert(response.msg || 'Failed to fetch video metadata.');
                        }
                        $btn.text(origText).prop('disabled', false);
                    },
                    error: function() {
                        alert('Failed to connect to the metadata server.');
                        $btn.text(origText).prop('disabled', false);
                    }
                });
            });

            function setRemoteThumbnail(url) {
                $('#remote_image').val(url);
                $('#remoteImagePath').attr('src', url);
                $('.featureImageUploadBox').hide();
                $('.featureImagePreviewBox').hide();
                $('.remoteImagePreviewBox').show();
            }

            function setEditorContent(html) {
                var $editorFrame = $('iframe#' + $('#title').parents('form').find('textarea.editor').attr('id'));
                if ($editorFrame.length && $editorFrame[0].contentWindow) {
                    var body = $editorFrame[0].contentWindow.document.body;
                    if (body) {
                        body.innerHTML = html + body.innerHTML;
                    }
                } else {
                    var $textarea = $('textarea.editor');
                    $textarea.val(html + $textarea.val());
                }
            }

            $('#remoteChangeImage').on('click', function(e) {
                e.preventDefault();
                $('#remote_image').val('');
                $('#remoteImagePath').attr('src', '');
                $('.remoteImagePreviewBox').hide();
                $('.featureImageUploadBox').show();
            });

            $('#featureImage').on('change', function() {
                if ($(this).val()) {
                    $('#remote_image').val('');
                    $('#remoteImagePath').attr('src', '');
                    $('.remoteImagePreviewBox').hide();
                }
            });
        });
    </script>
</div>