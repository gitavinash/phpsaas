<?php

$id = mysqli_real_escape_string($connection, ((isset($_GET['id'])) ? base64_decode($_GET['id']) : 0));
$category_image = '';
if ($id != 0) {

	if ($categoryData = mysqli_query($connection, "SELECT category_parent_id, category_title, category_description, category_image, link_url FROM " . TABLE_PREFIX . "category, " . TABLE_PREFIX . "link, " . TABLE_PREFIX . "meta WHERE category_id=link_relation_id AND meta_relation_id=link_id AND category_id='{$id}' AND link_type='category' AND category_type='{$postType}' AND link_status=200")) {

		if (mysqli_num_rows($categoryData) != 0) {

			extract(mysqli_fetch_assoc($categoryData));
		} else {

			$category_title = '';
			$category_description = '';
			$category_parent_id = '';
			$link_url = '';
		}
	}
} else {
	$category_title = '';
	$category_description = '';
	$category_parent_id = '';
	$link_url = '';
}

?>
<style>
/* ── Category page layout ─────────────────────────────────────── */
.cat-page-wrap {
    display: grid;
    grid-template-columns: 360px 1fr;
    gap: 24px;
    align-items: start;
}

@media (max-width: 860px) {
    .cat-page-wrap {
        grid-template-columns: 1fr;
    }
}

/* ── Left form panel ──────────────────────────────────────────── */
.cat-form-panel {
    background: #fff;
    border: 1px solid #d7e0e8;
    border-radius: 8px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, .04);
}

.cat-form-panel-head {
    padding: 14px 20px;
    border-bottom: 1px solid #eef3f7;
    font-size: 13px;
    font-weight: 600;
    color: #1f2d3a;
    background: #fafbfc;
    border-radius: 8px 8px 0 0;
}

.cat-form-panel-body {
    padding: 20px;
}

.cat-form-panel .form-group {
    margin-bottom: 15px;
}

.cat-form-panel .form-group:last-child {
    margin-bottom: 0;
}

.cat-form-panel label {
    display: block;
    font-size: 11.5px;
    font-weight: 600;
    color: #637282;
    margin-bottom: 5px;
    text-transform: uppercase;
    letter-spacing: .04em;
}

.cat-form-panel .form-control {
    width: 100%;
    padding: 8px 11px;
    font-size: 13px;
    border: 1px solid #d7e0e8;
    border-radius: 6px;
    box-sizing: border-box;
    outline: none;
    transition: border-color .12s, box-shadow .12s;
    color: #1f2d3a;
    background: #fff;
    font-family: inherit;
    line-height: 1.4;
}

.cat-form-panel .form-control:focus {
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, .1);
}

.cat-form-panel textarea.form-control {
    min-height: 76px;
    resize: vertical;
    line-height: 1.5;
}

.cat-form-panel-divider {
    height: 1px;
    background: #eef3f7;
    margin: 18px 0;
}

.cat-form-actions {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
    margin-top: 4px;
    float: left;
}

/* ── Feature image upload ─────────────────────────────────────── */
.cat-img-drop {
    position: relative;
    border: 1.5px dashed #d7e0e8;
    border-radius: 6px;
    padding: 22px 16px;
    text-align: center;
    cursor: pointer;
    transition: border-color .12s, background .12s;
    background: #fafbfc;
}

.cat-img-drop:hover {
    border-color: #3b82f6;
    background: #f0f5ff;
}

.cat-img-drop input[type="file"] {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    opacity: 0;
    cursor: pointer;
    z-index: 2;
}

.cat-img-drop-icon {
    font-size: 24px;
    color: #c5d2db;
    margin-bottom: 7px;
}

.cat-img-drop-title {
    font-size: 13px;
    font-weight: 500;
    color: #4a6070;
    margin-bottom: 3px;
}

.cat-img-drop-hint {
    font-size: 11px;
    color: #a0b3bf;
}

.cat-img-preview-box {
    position: relative;
    border-radius: 6px;
    overflow: hidden;
    border: 1px solid #d7e0e8;
}

.cat-img-preview-box img {
    display: block;
    width: 100%;
    max-height: 150px;
    object-fit: cover;
}

.cat-img-remove-btn {
    position: absolute;
    top: 7px;
    right: 7px;
    background: rgba(0, 0, 0, .52);
    color: #fff;
    border: none;
    border-radius: 4px;
    padding: 3px 10px;
    font-size: 11px;
    font-weight: 600;
    cursor: pointer;
}

.cat-img-remove-btn:hover {
    background: #dc2626;
}

/* ── Category cell — mirrors product-cell from list.php ───────── */
.cat-cell {
    display: flex;
    gap: 12px;
    align-items: flex-start;
}

.cat-thumb {
    width: 52px;
    height: 52px;
    border-radius: 8px;
    overflow: hidden;
    background: #eef3f7;
    flex-shrink: 0;
    border: 1px solid #d7e0e8;
    display: flex;
    align-items: center;
    justify-content: center;
}

.cat-thumb img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.cat-thumb-icon {
    font-size: 18px;
    color: #b0bec8;
}

.cat-meta {
    min-width: 0;
}

.cat-meta p {
    margin: 0;
}

.cat-title-link {
    font-size: 14px;
    font-weight: 600;
    line-height: 1.35;
    display: inline-block;
    max-width: 100%;
    word-break: break-word;
    color: #1f2d3a;
    text-decoration: none;
}

.cat-title-link:hover {
    color: #2563eb;
}

.cat-meta .hover {
    display: block !important;
    min-height: 18px;
    margin-top: 8px;
    opacity: 0;
    visibility: hidden;
    transition: opacity 0.15s ease;
}

#table tbody tr:hover .cat-meta .hover,
#table tbody tr:focus-within .cat-meta .hover {
    opacity: 1;
    visibility: visible;
}

.cat-tags {
    margin-top: 5px;
    display: flex;
    flex-wrap: wrap;
    gap: 5px;
}

.cat-tag {
    background: #f2f6fa;
    border: 1px solid #d9e2ea;
    color: #41505f;
    font-size: 11px;
    padding: 2px 7px;
    border-radius: 999px;
    line-height: 1.3;
}

.cat-tag-parent {
    background: #eff6ff;
    border-color: #bfdbfe;
    color: #2563eb;
}
</style>

<h2><?php echo ucwords(rtrim(ltrim(ADMIN_BASE_URL, '/'), '/')); ?> categories
    <?php if (isset($_GET['id'])): ?>
    <span><a href="<?php echo rtrim(ltrim(ADMIN_BASE_URL, '/'), '/') ?>/?type=category" class="btn btn-blue btn-sm">+
            Add New</a></span>
    <?php endif; ?>
</h2>

<div class="cat-page-wrap">

    <!-- ── Form panel ───────────────────────────────────────────── -->
    <div class="cat-form-panel">
        <div class="cat-form-panel-head">
            <?php echo isset($_GET['id']) ? 'Edit Category' : 'Add New Category'; ?>
        </div>
        <div class="cat-form-panel-body">
            <form id="categoryData" enctype="multipart/form-data">

                <div class="form-group">
                    <label>Name</label>
                    <input type="text" name="name" <?php echo !isset($_GET['id']) ? 'id="title"' : ''; ?>
                        class="form-control" placeholder="Category name"
                        value="<?php echo htmlspecialchars($category_title); ?>" autofocus>
                </div>

                <div class="form-group">
                    <label>Permalink</label>
                    <input type="text" name="url" id="url" class="form-control" placeholder="category-slug" value="<?php $categoryURL = explode('/', ltrim(rtrim($link_url, '/'), '/'));
																													echo end($categoryURL); ?>">
                </div>

                <div class="form-group">
                    <label>Parent Category</label>
                    <select name="category" class="form-control">
                        <option value="0">None (top level)</option>
                        <?php
						if ($categoryList = mysqli_query($connection, "SELECT category_id, category_title, link_url FROM " . TABLE_PREFIX . "category, " . TABLE_PREFIX . "link, " . TABLE_PREFIX . "meta WHERE category_id=link_relation_id AND link_type='category' AND meta_relation_id=link_id AND category_type='{$postType}' AND category_site_id='" . SITE_ID . "' AND link_status=200 ORDER BY category_id DESC")) {
							while ($category = mysqli_fetch_assoc($categoryList)) { ?>
                        <option
                            value="<?php echo $category['category_id'] . '_' . str_replace(SITE_DOMAIN . '/category', '', $category['link_url']); ?>"
                            <?php echo $category['category_id'] == $category_parent_id ? 'selected' : '' ?>>
                            <?php echo ucwords($category['category_title']); ?></option>
                        <?php }
						} ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" class="form-control"
                        placeholder="Short description…"><?php echo htmlspecialchars($category_description); ?></textarea>
                </div>

                <div class="cat-form-panel-divider"></div>

                <div class="form-group">
                    <label>Feature Image</label>
                    <div id="catImgUploadBox" class="cat-img-drop"
                        style="<?php echo $category_image !== '' ? 'display:none' : ''; ?>">
                        <div class="cat-img-drop-icon"><i class="fas fa-cloud-upload-alt"></i></div>
                        <div class="cat-img-drop-title">Click to upload</div>
                        <div class="cat-img-drop-hint">.jpg &middot; .png &middot; .gif</div>
                        <input type="hidden" name="oldFeatureImage"
                            value="<?php echo htmlspecialchars($category_image); ?>">
                        <input type="file" id="catFeatureImage" name="featureImage"
                            accept="image/png,image/jpeg,image/gif">
                    </div>
                    <div id="catImgPreviewBox" class="cat-img-preview-box"
                        style="<?php echo $category_image !== '' ? '' : 'display:none'; ?>">
                        <img id="catImgPreviewEl"
                            src="<?php echo $category_image !== '' ? '//' . SITE_DOMAIN . htmlspecialchars($category_image) : ''; ?>"
                            alt="">
                        <button type="button" class="cat-img-remove-btn" id="catImgRemoveBtn">&#10005; Remove</button>
                        <input type="hidden" name="oldFeatureImage" id="catOldFeatureImage"
                            value="<?php echo htmlspecialchars($category_image); ?>">
                    </div>
                </div>

                <input type="hidden" name="type" value="category">
                <?php if (isset($_GET['id'])): ?>
                <input type="hidden" name="oldURL" value="<?php echo htmlspecialchars($link_url); ?>">
                <?php endif; ?>
                <input type="hidden" name="postType" id="type" value="<?php echo htmlspecialchars($postType); ?>">
                <input type="hidden" name="action" value="publish">
                <input type="hidden" name="categoryID" id="category_id" value="<?php echo $id; ?>">

                <div class="cat-form-panel-divider"></div>

                <div class="cat-form-actions" style="margin-top:0">
                    <button type="button" id="submitCategory" class="btn btn-green">
                        <?php echo isset($_GET['id']) ? 'Update Category' : 'Add Category'; ?>
                    </button>
                    <?php if (isset($_GET['id'])): ?>
                    <a href="<?php echo $postType; ?>/?type=taxonomy&editor=true&id=<?php echo base64_encode($id); ?>&taxonomy=category"
                        class="btn btn-blue">Advanced Edit</a>
                    <?php endif; ?>
                </div>

            </form>
        </div>
    </div>

    <!-- ── Category list table ──────────────────────────────────── -->
    <div>
        <table id="table">
            <thead>
                <tr>
                    <th>Category</th>
                    <th style="width:110px">Slug</th>
                </tr>
            </thead>
            <tbody>
                <?php
				if ($dataList = mysqli_query($connection, "SELECT c.category_id, c.category_image, c.category_title, c.category_parent_id, l.link_url, p.category_title AS parent_title FROM " . TABLE_PREFIX . "category c JOIN " . TABLE_PREFIX . "link l ON c.category_id=l.link_relation_id JOIN " . TABLE_PREFIX . "meta m ON m.meta_relation_id=l.link_id LEFT JOIN " . TABLE_PREFIX . "category p ON p.category_id=c.category_parent_id AND c.category_parent_id != 0 WHERE l.link_type='category' AND c.category_type='{$postType}' AND c.category_site_id='" . SITE_ID . "' AND l.link_status=200 ORDER BY c.category_title ASC")) {
					if (mysqli_num_rows($dataList) != 0) {
						while ($data = mysqli_fetch_assoc($dataList)) {
							$cat_img          = $data['category_image'];
							$cat_id           = $data['category_id'];
							$cat_title        = $data['category_title'];
							$cat_url          = $data['link_url'];
							$cat_parent_title = $data['parent_title'];
							$cat_url_parts    = array_filter(explode('/', $cat_url));
							$cat_slug         = end($cat_url_parts);
				?>
                <tr style="height:72px">
                    <td>
                        <div class="cat-cell">
                            <div class="cat-thumb">
                                <?php if ($cat_img !== ''): ?>
                                <img src="//<?php echo SITE_DOMAIN . htmlspecialchars($cat_img); ?>"
                                    alt="<?php echo htmlspecialchars($cat_title); ?>">
                                <?php else: ?>
                                <i class="fas fa-tag cat-thumb-icon"></i>
                                <?php endif; ?>
                            </div>
                            <div class="cat-meta">
                                <p><a class="cat-title-link"
                                        href="<?php echo $postType; ?>/?type=category&id=<?php echo base64_encode($cat_id); ?>"><?php echo htmlspecialchars(ucwords($cat_title)); ?></a>
                                </p>
                                <div class="cat-tags">
                                    <span class="cat-tag"><?php echo htmlspecialchars(ucwords($postType)); ?></span>
                                    <?php if (!empty($cat_parent_title)): ?>
                                    <span class="cat-tag cat-tag-parent"><i class="fas fa-level-up-alt"></i>
                                        <?php echo htmlspecialchars(ucwords($cat_parent_title)); ?></span>
                                    <?php endif; ?>
                                </div>
                                <div class="hover">
                                    <a
                                        href="<?php echo $postType; ?>/?type=category&id=<?php echo base64_encode($cat_id); ?>"><i
                                            class="fas fa-pencil-alt"></i> Edit</a>
                                    <a
                                        href="<?php echo $postType; ?>/?type=taxonomy&editor=true&id=<?php echo base64_encode($cat_id); ?>&taxonomy=category"><i
                                            class="fas fa-pencil-alt"></i> Advanced</a>
                                    <?php if ($cat_id != 1): ?>
                                    <a href="#" id="<?php echo base64_encode($cat_id) . '_' . $type; ?>"
                                        class="deleteBtn"><i class="fas fa-trash"></i> Delete</a>
                                    <?php endif; ?>
                                    <a href="<?php echo htmlspecialchars($cat_url); ?>" target="_blank"><i
                                            class="fas fa-eye"></i> View</a>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td style="color:#637282;font-size:12px"><?php echo htmlspecialchars($cat_slug); ?></td>
                </tr>
                <?php
						}
					}
				} else {
					echo mysqli_error($connection);
				}
				?>
            </tbody>
        </table>
    </div>

</div>

<script>
(function() {
    var fileInput = document.getElementById('catFeatureImage');
    var uploadBox = document.getElementById('catImgUploadBox');
    var previewBox = document.getElementById('catImgPreviewBox');
    var previewImg = document.getElementById('catImgPreviewEl');
    var removeBtn = document.getElementById('catImgRemoveBtn');
    var oldInput = document.getElementById('catOldFeatureImage');

    if (fileInput) {
        fileInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    uploadBox.style.display = 'none';
                    previewBox.style.display = 'block';
                };
                reader.readAsDataURL(this.files[0]);
            }
        });
    }

    if (removeBtn) {
        removeBtn.addEventListener('click', function() {
            previewImg.src = '';
            if (fileInput) fileInput.value = '';
            if (oldInput) oldInput.value = '';
            previewBox.style.display = 'none';
            uploadBox.style.display = 'block';
        });
    }

    var submitBtn = document.getElementById('submitCategory');
    if (submitBtn) {
        submitBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopImmediatePropagation(); // prevent global script.js handler firing a second AJAX
            var nameVal = document.querySelector('#categoryData [name="name"]').value.trim();
            if (!nameVal) {
                if (typeof popUpMessage === 'function') popUpMessage('center',
                    'Please enter a category name.', 'warning');
                return;
            }
            submitBtn.disabled = true;
            var fd = new FormData(document.getElementById('categoryData'));
            var categoryID = document.getElementById('category_id').value;
            fd.set('action', (categoryID != 0) ? 'update' : 'publish');

            $.ajax({
                type: 'POST',
                dataType: 'json',
                data: fd,
                processData: false,
                contentType: false,
                success: function(data) {
                    if (data.status == 200) {
                        if (typeof popUpMessage === 'function') popUpMessage('center', data.msg,
                            data.status);
                        setTimeout(function() {
                            if (data.redirect) {
                                window.location.href = data.redirect;
                            } else {
                                var t = document.getElementById('type').value;
                                window.location.href = '/dashboard/' + t +
                                    '/?type=category&id=' + btoa(data.categoryID);
                            }
                        }, 1400);
                    } else {
                        if (typeof popUpMessage === 'function') popUpMessage('center', data.msg,
                            data.status);
                        submitBtn.disabled = false;
                    }
                },
                error: function() {
                    submitBtn.disabled = false;
                }
            });
        });
    }
}());
</script>