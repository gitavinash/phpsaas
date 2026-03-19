<?php

$id = mysqli_real_escape_string($connection, ((isset($_GET['id'])) ? base64_decode($_GET['id']) : 0));

if ($id != 0) {

    if ($categoryData = mysqli_query($connection, "SELECT category_parent_id, category_title, category_description, link_url FROM " . TABLE_PREFIX . "category, " . TABLE_PREFIX . "link, " . TABLE_PREFIX . "meta WHERE category_id=link_relation_id AND meta_relation_id=link_id AND category_id='{$id}' AND link_type='category' AND category_type='{$postType}' AND link_status=200")) {

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
}

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
</style>

<h2><?php echo ucwords(rtrim(ltrim(ADMIN_BASE_URL, '/'), '/')); ?> categories
    <?php echo ((isset($_GET['id'])) ? "<span><a href='" . rtrim(ltrim(ADMIN_BASE_URL, '/'), '/') . "/?type=category' class='btn btn-blue btn-sm' >+ Add New</a></span>" : '') ?>
</h2>

<div class="cat-page-wrap">
    <div class="cat-form-panel">
        <div class="cat-form-panel-head">
            <?php echo ((isset($_GET['id'])) ? 'Edit Category' : 'Add New Category'); ?>
        </div>
        <div class="cat-form-panel-body">
            <form method="POST" id="categoryData" onSubmit="submitCategory()">
                <div class="form-group">
                    <label>Name</label>
                    <input type="text" name="name" <?php echo ((!isset($_GET['id'])) ? 'id="title"' : ''); ?>
                        class="form-control" placeholder="Category name"
                        value="<?php echo htmlspecialchars($category_title); ?>" autofocus>
                </div>
                <div class="form-group">
                    <label>Permalink</label>
                    <input type="text" name="url" id='url' class="form-control" placeholder="category-slug" value="<?php $categoryURL = explode('/', ltrim(rtrim($link_url, '/'), '/'));
                                echo end($categoryURL); ?>">
                </div>
                <div class="form-group">
                    <label>Parent Category</label>
                    <select name="category" class="form-control">
                        <option value="0">None (top level)</option>
                        <?php
                        if ($categoryList = mysqli_query($connection, "SELECT category_id, category_title, link_url FROM " . TABLE_PREFIX . "category, " . TABLE_PREFIX . "link WHERE category_site_id='" . APP_SITE_ID . "' AND link_type='category' AND link_relation_id=category_id AND category_id!='{$id}' AND link_status=200 AND category_status=1 AND category_type='" . rtrim(ltrim(ADMIN_BASE_URL, '/'), '/') . "'")) {
                            while ($category = mysqli_fetch_assoc($categoryList)) { ?>
                        <option
                            value="<?php echo $category['category_id'] . '_' . str_replace(SITE_DOMAIN . '/category', '', $category['link_url']); ?>"
                            <?php echo (($category['category_id'] == $category_parent_id) ? 'selected' : '') ?>>
                            <?php echo ucwords($category['category_title']); ?></option>
                        <?php
                            }
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" class="form-control"
                        placeholder="Short description..."><?php echo htmlspecialchars($category_description); ?></textarea>
                </div>

                <div class="cat-form-panel-divider"></div>

                <div class="form-group">
                    <input type="hidden" name="type" value="category">
                    <?php
                    if (isset($_GET['id'])) {
                        echo "<input type='hidden' name='oldURL' value='" . htmlspecialchars($link_url) . "' >";
                    }
                    ?>
                    <input type="hidden" name="postType" id="type" value="<?php echo htmlspecialchars($postType); ?>">
                    <input type="hidden" name="action" value="publish">
                    <input type="hidden" name="categoryID" id="category_id" value="<?php echo $id; ?>">

                    <div class="cat-form-actions" style="margin-top:0">
                        <input type="submit" id="submitCategory" class="btn btn-green"
                            value="<?php echo (isset($_GET['id']) ? 'Update' : 'Add'); ?> Category">
                        <?php
                        if (isset($_GET['id'])) {
                            echo "<a href='{$postType}/?type=taxonomy&editor=true&id={$id}&taxonomy=category' class='btn btn-blue'>Advanced Edit</a>";
                        }
                        ?>
                    </div>
                </div>
            </form>
        </div>
    </div>
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

                if ($dataList = mysqli_query($connection, "SELECT category_id, category_description, category_title, link_url FROM " . TABLE_PREFIX . "category, " . TABLE_PREFIX . "link, " . TABLE_PREFIX . "meta WHERE category_id=link_relation_id AND link_type='category' AND meta_relation_id=link_id AND category_type='{$postType}' AND category_site_id='" . SITE_ID . "' AND link_status=200 ORDER BY category_id DESC")) {
                    if (mysqli_num_rows($dataList) != 0) {
                        while ($data = mysqli_fetch_assoc($dataList)) {
                            extract($data);
                            $cat_url_parts = array_filter(explode('/', $link_url));
                            $cat_slug = end($cat_url_parts);
                ?>
                <tr style="height:72px">
                    <td>
                        <div class="cat-cell">
                            <div class="cat-thumb">
                                <i class="fas fa-tag cat-thumb-icon"></i>
                            </div>
                            <div class="cat-meta">
                                <p><a class="cat-title-link"
                                        href="<?php echo $postType ?>/?type=category&id=<?php echo base64_encode($category_id); ?>"><?php echo htmlspecialchars(ucwords($category_title)); ?></a>
                                </p>
                                <div class="cat-tags">
                                    <span class="cat-tag"><?php echo htmlspecialchars(ucwords($postType)); ?></span>
                                </div>
                                <div class="hover">
                                    <a
                                        href="<?php echo $postType ?>/?type=category&id=<?php echo base64_encode($category_id); ?>"><i
                                            class="fas fa-pencil-alt"></i> Edit</a>
                                    <a
                                        href="<?php echo $postType ?>/?type=taxonomy&editor=true&id=<?php echo base64_encode($category_id); ?>&taxonomy=category"><i
                                            class="fas fa-pencil-alt"></i> Advanced</a>
                                    <a href="#" id="<?php echo base64_encode($category_id) ?>_category_news"
                                        class="deleteBtn"><i class="fas fa-trash"></i> Delete</a>
                                    <a href="<?php echo htmlspecialchars($link_url); ?>" target='_blank'><i
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