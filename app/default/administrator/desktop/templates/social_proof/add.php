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
<div class="product-add-ui">
    <h2>Add new <?php echo str_replace('_', ' ', rtrim(ltrim(ADMIN_BASE_URL, '/'), '/')); ?></h2>

    <!-- Initial Video URL Import Step -->
    <div id="url_fetch_container" style="background: #fff; padding: 40px; border-radius: 8px; max-width: 600px; margin: 30px auto; text-align: center; border: 1px solid #e5e7eb; box-shadow: 0 4px 6px rgba(0,0,0,0.05); display: block; clear: both;">
        <i class="fas fa-play-circle" style="font-size: 48px; color: #2563eb; margin-bottom: 20px; display: block; margin-left: auto; margin-right: auto;"></i>
        <h3 style="margin-bottom: 10px; font-weight: 600; font-size: 20px; color: #1f2937;">Import from Social Video URL</h3>
        <p style="color: #6b7280; font-size: 14px; margin-bottom: 25px;">Paste a YouTube Shorts, Instagram Reel, or TikTok URL to auto-populate the post details.</p>

        <div style="margin-bottom: 30px; width: 100%; display: block; clear: both; float: none;">
            <input type="url" id="initial_video_url" class="form-control inside-shadow" placeholder="Paste video link here..." style="font-size: 16px; padding: 12px 15px; text-align: center; width: 100%; max-width: 100%; box-sizing: border-box; display: block;" autofocus>
        </div>

        <div style="display: flex; gap: 15px; justify-content: center; align-items: center; width: 100%; clear: both; float: none; margin-top: 25px;padding: 10px 0px;box-sizing: border-box;">
            <button type="button" id="btn_initial_fetch" class="btn btn-green" style="padding: 12px 25px; font-weight: 600; font-size: 14px; display: inline-block;">Continue</button>
            <button type="button" id="btn_skip_fetch" class="btn btn-yellow" style="padding: 12px 25px; font-weight: 600; font-size: 14px; display: inline-block;">Skip</button>
        </div>
    </div>

    <div class="form-box" id="full_form_container" style="display: none;">
        <form method="POST" id="formData" enctype="multipart/form-data">
            <div class="left-box">
                <div class="form-group">
                    <div id="video_url_input_container" style="width: 100%; float: left; clear: both; margin-bottom: 5px;">
                        <label>Video URL (YouTube Shorts, Instagram Reels, TikTok)</label>
                        <div style="width: 100%; float: left; clear: both; margin-bottom: 5px;">
                            <input type="url" id="video_url" class="form-control inside-shadow" name="video_url" placeholder="https://www.youtube.com/shorts/... or Instagram Reel or TikTok URL" style="width: 80%; float: left; margin: 0; padding: 7px 10px; height: 38px; box-sizing: border-box;" autofocus>
                            <button type="button" id="btn_fetch_video" class="btn btn-blue" style="width: 18%; float: right; margin: 0; height: 38px; line-height: 22px; padding: 7px 15px; box-sizing: border-box; font-size: 14px; white-space: nowrap;">Fetch Details</button>
                        </div>
                        <small style="color: #666; display: block; margin-top: 5px; clear: both;">Enter a video URL to automatically fetch its Title, Thumbnail, and Embed player (or skip to enter manually).</small>
                    </div>
                    <div id="entered_url_display_container" style="margin-top: 8px; font-size: 13px; display: none; clear: both;">
                        <strong style="color: #333;">Entered Video URL:</strong>
                        <a href="" id="entered_url_link" target="_blank" style="color: #00609e; text-decoration: underline; word-break: break-all; margin-left: 5px;"></a>
                        <a href="javascript:void(0);" id="btn_change_url" style="color: #dc3545; font-weight: bold; text-decoration: underline; margin-left: 15px;">Change URL</a>
                    </div>
                </div>
                <div class="form-group">
                    <input type="text" id="title" class="form-control inside-shadow" name="title" placeholder="Title"
                        required>
                </div>
                <div class="form-group">
                    <div class="url">
                        <p><strong>Permalink</strong></p>
                        <p><span><?php echo SITE_DOMAIN ?>/</span></p>
                        <input type="text" id="url" class="form-control" name="url">
                    </div>
                </div>
                <div class="form-group fullscreen-editor">
                    <textarea name="description" class="editor inside-shadow"></textarea>
                </div>

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
                                placeholder="Meta title">
                        </div>
                        <div class="form-group">
                            <label>Meta Description</label>
                            <textarea name="meta_desc" id="meta_desc" class="form-control inside-shadow"
                                placeholder="Meta Description"></textarea>
                        </div>
                        <div class="form-group">
                            <label>Meta Keywords</label>
                            <textarea name="meta_key" id="meta_keywords" class="form-control inside-shadow"
                                placeholder="Enter keywords of the post ( use , to seprate keywords )"></textarea>
                        </div>
                        <div class="form-group">
                            <label>Canonical URL</label>
                            <input type="text" name="canonical" class="form-control inside-shadow" id="canonical"
                                placeholder="Actual URL of the <?php echo $postType ?>">
                        </div>
                        <div class="form-group">
                            <label>Allow search engines to show this Post in search results?</label>
                            <select name="robots" class="form-control inside-shadow">
                                <option value="index, follow">Yes</option>
                                <option value="noindex, nofollow">No</option>
                            </select>
                        </div>
                    </div>
                    <div class="fb generalTab" style="display: none;">
                        <div class="facebookPreviewBox" style="display: none;">
                            <div class="img"><img src="" id="facebookPath" alt=""></div>
                            <p id="facebookChangeImage" class='changeImage'>Remove facebook feature image</p>
                        </div>
                        <div class="facebookUploadBox">
                            <span>Set facebook feature image</span>
                            <input type='file' id="facebookImage" name='facebookImage' />
                        </div>
                        <div class="form-group">
                            <label>Facebook Title</label>
                            <input type="text" name="fb_title" id="fb_title" class="form-control inside-shadow"
                                placeholder="Facebook title">
                        </div>
                        <div class="form-group">
                            <label>Facebook Description</label>
                            <textarea name="fb_desc" id="fb_desc" class="form-control inside-shadow"
                                placeholder="Facebook Description"></textarea>
                        </div>
                    </div>
                    <div class="tw generalTab" style="display: none;">
                        <div class="twitterPreviewBox" style="display: none;">
                            <div class="img"><img src="" id="twitterPath" alt=""></div>
                            <p id="twitterChangeImage" class="changeImage">Remove twitter feature image</p>
                        </div>
                        <div class="twitterUploadBox">
                            <span>Set Twitter feature image</span>
                            <input type='file' id="twitterImage" name='twitterImage' />
                        </div>
                        <div class="form-group">
                            <label>Twitter Title</label>
                            <input type="text" name="tw_title" id="tw_title" class="form-control inside-shadow"
                                placeholder="Twitter title">
                        </div>
                        <div class="form-group">
                            <label>Twitter Description</label>
                            <textarea name="tw_desc" id="tw_desc" class="form-control inside-shadow"
                                placeholder="Twitter Description"></textarea>
                        </div>
                    </div>
                </section>

            </div>
            <div class="right-box">
                <div class="action-box inside-shadow">
                    <input type="button" id="publishPost" class="btn btn-green btn-block" value="Publish">
                    <!-- <input type="submit" id="draftPost" class="btn btn-yellow btn-block" value="Save Draft"> -->
                    <input type="hidden" name="type" value="<?php echo rtrim(ltrim(ADMIN_BASE_URL, '/'), '/'); ?>">
                    <input type="hidden" name="action" value="publish">
                </div>
                <div class="form-group">
                    <h2>Featured</h2>
                    <div class="action-box inside-shadow" style="padding: 10px">
                        <input type="checkbox" value="1" name='featurePost'> Mark as feature
                    </div>
                </div>
                <h2>Categories</h2>
                <div class="action-box height-200 inside-shadow">
                    <?php
                    if ($categoryList = mysqli_query($connection, "SELECT category_id, category_title, link_url FROM " . TABLE_PREFIX . "category, " . TABLE_PREFIX . "link, " . TABLE_PREFIX . "meta WHERE category_site_id='" . SITE_ID . "' AND meta_relation_id=link_id AND link_type='category' AND link_relation_id=category_id AND link_status=200 AND category_status=1 AND category_type='social_proof' AND link_site_id=" . SITE_ID)) {
                        while ($category = mysqli_fetch_assoc($categoryList)) { ?>
                            <div class="form-group">
                                <input type="radio" name="category"
                                    value="<?php echo $category['category_id'] . '_' . str_replace('/category', '', $category['link_url']); ?>"
                                    <?php echo (($category['category_id'] == 1) ? 'checked' : '') ?>>
                                <?php echo ucwords($category['category_title']); ?>
                            </div>
                    <?php
                        }
                    }
                    ?>
                </div>
                <h2>Tags</h2>
                <div class="action-box inside-shadow">
                    <div class="form-group">
                        <input type="text" class='tags' />
                        <input type="hidden" id='tags' name="tags" value=''>
                    </div>
                </div>
                <div class="tagList" style="display: none;"></div>
                <h2>Link to Product / Service / Page</h2>
                <div class="action-box inside-shadow" style="padding: 10px;">
                    <div class="form-group" style="margin-bottom: 0; position: relative;">
                        <select name="link_url_ref" class="selectJS form-control" id="link_url_ref">
                            <option value="">Select linked Product or Service</option>
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
                                    echo '<option value="' . htmlspecialchars($item['link_relation_id']) . '_' . htmlspecialchars($item['link_type']) . '">';
                                    echo '[' . $item['item_type'] . '] ' . htmlspecialchars($item['title']);
                                    echo '</option>';
                                }
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <h2>Feature Image</h2>
                <div class="form-group featureImageUploadBox upload-btn">
                    <span>Set Feature Image</span>
                    <input type="file" id="featureImage" name="featureImage" accept="image/x-png, image/gif, image/jpeg">
                    <p><strong>Only these file formats are supported:</strong> .png, .jpg, .jpeg, .gif <strong>Recommended
                            file size maximum 500kb</p>
                </div>
                <div class="img-preview featureImagePreviewBox action-box" style="display: none;">
                    <img src="#" id="featureImagePath" alt="">
                    <p id="featureChangeImage" class="btn btn-red btn-sm">Remove feature image</p>
                </div>
                <div class="img-preview remoteImagePreviewBox action-box" style="display: none; padding: 10px;">
                    <img src="" id="remoteImagePath" alt="" referrerpolicy="no-referrer" style="max-width: 100%; height: auto; display: block; margin-bottom: 10px; border-radius: 4px;">
                    <p id="remoteChangeImage" class="btn btn-red btn-sm">Remove remote image</p>
                </div>
                <input type="hidden" name="remote_image" id="remote_image" value="">
            </div>
        </form>

        <script type="text/javascript">
            document.addEventListener("DOMContentLoaded", function() {
                if (typeof jQuery === "undefined") return;
                var $ = jQuery;

                // Helper to update entered URL display
                function updateEnteredUrlDisplay() {
                    var url = $.trim($('#video_url').val());
                    if (url) {
                        $('#entered_url_link').attr('href', url).text(url);
                        $('#entered_url_display_container').show();
                        $('#video_url_input_container').hide();
                    } else {
                        $('#entered_url_display_container').hide();
                        $('#video_url_input_container').show();
                    }
                }

                // Monitor input changes on #video_url (only on change / blur to prevent hiding mid-typing)
                $('#video_url').on('change blur', function() {
                    updateEnteredUrlDisplay();
                });

                // Call initially to set correct state
                updateEnteredUrlDisplay();

                // Change URL and reload page
                $(document).on('click', '#btn_change_url', function(e) {
                    e.preventDefault();
                    window.location.reload();
                });

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
                    if ($select.hasClass("select2-hidden-accessible")) {
                        $select.select2("destroy");
                    }
                    $select.select2({
                        placeholder: "Select linked Product or Service",
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

                // Core Fetch Function
                function fetchVideoMetadata(videoUrl, successCallback, errorCallback) {
                    $.ajax({
                        url: '/api/social_proof/meta/',
                        type: 'GET',
                        data: {
                            url: videoUrl
                        },
                        dataType: 'json',
                        success: function(response) {
                            if (response && response.status == 200) {
                                successCallback(response);
                            } else {
                                errorCallback(response.msg || 'Failed to fetch video metadata.');
                            }
                        },
                        error: function() {
                            errorCallback('Failed to connect to the metadata server.');
                        }
                    });
                }

                // Assign helper
                function assignFetchedData(data) {
                    if (data.title) {
                        $('#title').val(data.title).trigger('keyup');
                    }
                    if (data.thumbnail) {
                        setRemoteThumbnail(data.thumbnail);
                    }
                    if (data.embed) {
                        var html = data.embed;
                        if (data.description && data.description !== data.title && data.description !== 'Instagram Reel' && data.description !== 'Instagram Reel') {
                            html += '<p>' + data.description + '</p>';
                        }
                        setEditorContent(html);
                    }
                }

                // Initial Fetch Button Action
                $('#btn_initial_fetch').on('click', function(e) {
                    e.preventDefault();
                    var url = $.trim($('#initial_video_url').val());
                    if (!url) {
                        alert('Please enter a video URL first.');
                        return;
                    }

                    var $btn = $(this);
                    var origText = $btn.text();
                    $btn.text('Fetching...').prop('disabled', true);

                    fetchVideoMetadata(url, function(data) {
                        $('#video_url').val(url).trigger('change');
                        assignFetchedData(data);

                        // Switch view
                        $('#url_fetch_container').hide();
                        $('#full_form_container').fadeIn();
                        $btn.text(origText).prop('disabled', false);
                    }, function(errorMsg) {
                        alert(errorMsg);
                        $btn.text(origText).prop('disabled', false);
                    });
                });

                // Skip Button Action
                $('#btn_skip_fetch').on('click', function(e) {
                    e.preventDefault();
                    var url = $.trim($('#initial_video_url').val());
                    if (url) {
                        $('#video_url').val(url).trigger('change');
                    }
                    $('#url_fetch_container').hide();
                    $('#full_form_container').fadeIn();
                });

                // Inline Main Form Fetch Button Action
                $('#btn_fetch_video').on('click', function(e) {
                    e.preventDefault();
                    var url = $.trim($('#video_url').val());
                    if (!url) {
                        alert('Please enter a video URL first.');
                        return;
                    }

                    var $btn = $(this);
                    var origText = $btn.text();
                    $btn.text('Fetching...').prop('disabled', true);

                    fetchVideoMetadata(url, function(data) {
                        assignFetchedData(data);
                        $btn.text(origText).prop('disabled', false);
                    }, function(errorMsg) {
                        alert(errorMsg);
                        $btn.text(origText).prop('disabled', false);
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