<?php

$id = base64_decode(mysqli_real_escape_string($connection, ((isset($_GET['id'])) ? $_GET['id'] : 0)));

if ($id != 0) {

    if ($categoryData = mysqli_query($connection, "SELECT category_parent_id, category_title, category_description, link_url FROM " . TABLE_PREFIX . "category, " . TABLE_PREFIX . "link, " . TABLE_PREFIX . "meta WHERE category_id=link_relation_id AND meta_relation_id=link_id AND category_id='{$id}' AND link_type='category' AND category_type='{$postType}' AND link_status=200")) {

        if (mysqli_num_rows($categoryData) != 0) {

            extract(mysqli_fetch_assoc($categoryData));
        }
    }
} else {

    $category_title = '';
    $category_description = '';
    $category_parent_id = '';
    $link_url = '';
}

?>
<h2>Add new brand
    <?php echo ((isset($_GET['id'])) ? "<span><a href='" . rtrim(ltrim(ADMIN_BASE_URL, '/'), '/') . "/?type=category' class='btn btn-blue btn-sm' >Add new brand</a></span>" : '') ?>
</h2>

<div class="row">
    <div class="category">
        <div class="form-box">
            <form method="POST" id="categoryData" onSubmit="submitCategory()">
                <h3><?php echo ((isset($_GET['id'])) ? 'Update' : 'Add new') ?> brand</h3>
                <div class="form-group">
                    <label>Name</label>
                    <input type="text" name="name" <?php echo ((!isset($_GET['id'])) ? 'id="title"' : ''); ?>
                        class="form-control inside-shadow" placeholder="Brand name..."
                        value="<?php echo $category_title; ?>" autofocus>
                </div>
                <div class="form-group">
                    <label>Permalink</label>
                    <input type="text" name="url" id='url' class="form-control inside-shadow" placeholder="brand url..."
                        value="<?php $categoryURL = explode('/', ltrim(rtrim($link_url, '/'), '/'));
                                                                                                                                echo end($categoryURL); ?>">
                </div>
                <div class="form-group">
                    <label>Parent brand</label>
                    <select name="category" class="form-control inside-shadow">
                        <option value="0">Select Parent Brand</option>
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
                    <textarea name="description" class="form-control inside-shadow"
                        placeholder="About brand..."><?php echo $category_description; ?></textarea>
                </div>
                <div class="form-group">
                    <input type="hidden" name="type" value="category">
                    <?php
                    if (isset($_GET['id'])) {
                        echo "<input type='hidden' name='oldURL' value='{$link_url}' >";
                    }
                    ?>
                    <input type="hidden" name="postType" id="type" value="<?php echo $postType ?>">
                    <input type="hidden" name="action" value="publish">
                    <input type="hidden" name="categoryID" id="category_id" value="<?php echo $id; ?>">
                    <input type="submit" id="submitCategory" class="btn btn-green"
                        value="<?php echo (isset($_GET['id']) ? 'Update' : 'Add'); ?> Brand">
                </div>
            </form>
        </div>
        <div class="category-list">
            <table id="table">
                <thead>
                    <tr>
                        <th style='width: 200px;'>Title</th>
                        <!-- <th style='width: 50px'>Count</th> -->
                    </tr>
                </thead>
                <tbody>
                    <?php

                    if ($dataList = mysqli_query($connection, "SELECT category_id, category_description, category_title, link_url FROM " . TABLE_PREFIX . "category, " . TABLE_PREFIX . "link, " . TABLE_PREFIX . "meta WHERE category_id=link_relation_id AND link_type='category' AND meta_relation_id=link_id AND category_type='{$postType}' AND category_site_id='" . APP_SITE_ID . "' AND link_status=200 ORDER BY category_title ASC")) {
                        if (mysqli_num_rows($dataList) != 0) {
                            while ($data = mysqli_fetch_assoc($dataList)) {
                                extract($data) ?>
                    <tr>
                        <td>
                            <p><a
                                    href="<?php echo $postType ?>/?type=taxonomy&editor=true&id=<?php echo base64_encode($category_id); ?>&taxonomy=category"><?php echo $category_title ?></a>
                            </p>
                            <div class="hover">
                                <a
                                    href="<?php echo $postType ?>/?type=taxonomy&editor=true&id=<?php echo base64_encode($category_id); ?>&taxonomy=category"><i
                                        class="fas fa-pencil-alt"></i> Advanced Edit</a>
                                <a
                                    href="<?php echo $postType ?>/?type=category&id=<?php echo base64_encode($category_id); ?>"><i
                                        class="fas fa-pencil-alt"></i> Edit</a>
                                <a href="#" id="<?php echo base64_encode($category_id) ?>_category_post"
                                    class="deleteBtn"><i class="fas fa-trash"></i> Delete</a>
                                <a href="<?php echo $link_url; ?>" target='_blank'><i class="fas fa-eye"></i> View</a>
                            </div>
                        </td>
                        <!-- <td><?php echo $category_total_count; ?></td> -->
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
</div>