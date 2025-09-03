<h2 class="heading">
    Category list
    <span>All categories will be listed here</span>
</h2>

<div class="shortcut">
    <a href="#" class="addCategoryPopUp" title="Add category">
        <i class="fi fi-rr-plus"></i>
    </a>
</div>

<div class="main-box">
    <section>
        <div class="list-box">
            <table class="showInventoryTableList display nowrap" style="width:100%;">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Parent</th>
                        <th>Products</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Small esc helper
                    $esc = fn($s) => htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');

                    // Build the query once
                    $getCategoryQuery = "
SELECT
    c.category_id,
    c.category_title,
    c.category_parent_id,
    c.category_status,
    p.category_title AS parent_category_name,

    COALESCE(self_cnt.products_self, 0)        AS products_self,
    COALESCE(kids_cnt.products_in_children, 0) AS products_children,
    COALESCE(self_cnt.products_self, 0)
      + COALESCE(kids_cnt.products_in_children, 0) AS products_total,
    COALESCE(kids_meta.child_categories, 0)    AS child_categories_count,
    c.category_status
FROM " . TABLE_PREFIX . "category AS c
LEFT JOIN " . TABLE_PREFIX . "category AS p
       ON p.category_id = c.category_parent_id

LEFT JOIN (
    SELECT
        pr.product_category AS category_id,
        COUNT(pr.product_id) AS products_self
    FROM " . TABLE_PREFIX . "product pr
    WHERE pr.product_status = 1
    GROUP BY pr.product_category
) AS self_cnt
  ON self_cnt.category_id = c.category_id

LEFT JOIN (
    SELECT
        ch.category_parent_id AS parent_id,
        COUNT(pr.product_id)   AS products_in_children
    FROM " . TABLE_PREFIX . "category ch
    LEFT JOIN " . TABLE_PREFIX . "product pr
           ON pr.product_category = ch.category_id
          AND pr.product_status = 1
    WHERE ch.category_status = 1
      AND ch.category_site_id =" . SITE_ID . "
    GROUP BY ch.category_parent_id
) AS kids_cnt
  ON kids_cnt.parent_id = c.category_id

LEFT JOIN (
    SELECT
        category_parent_id AS parent_id,
        COUNT(*)           AS child_categories
    FROM " . TABLE_PREFIX . "category
    WHERE category_status = 1
      AND category_site_id =" . SITE_ID . "
    GROUP BY category_parent_id
) AS kids_meta
  ON kids_meta.parent_id = c.category_id

WHERE c.category_status = 1
  AND c.category_site_id =" . SITE_ID . "
ORDER BY
    COALESCE(p.category_title, c.category_title) ASC,
    (c.category_parent_id IS NULL) DESC,
    c.category_title ASC
";

                    // Run and echo
                    if ($res = mysqli_query($connection, $getCategoryQuery)) {
                        if (mysqli_num_rows($res) > 0) {
                            while ($row = mysqli_fetch_assoc($res)) {
                                $catId    = (int)$row['category_id'];
                                $name     = $esc($row['category_title']);
                                $parent   = $row['parent_category_name'] ? $esc($row['parent_category_name']) : '—';

                                $self     = (int)$row['products_self'];
                                $children = (int)$row['products_children'];
                                $total    = (int)$row['products_total'];
                                $kids     = (int)$row['child_categories_count'];

                                $status   = ((int)$row['category_status'] === 1) ? 'Active' : 'Inactive';

                                // Compose “Products” cell similar to your reference UI:
                                // total as primary pill, show breakdown as title tooltip
                                $productsHtml = '
                <a class="badge badge-soft-primary" href="product/list/?category=' . $catId . '" title="Self: ' . $self . ' | Children: ' . $children . '">
                    ' . $total . ' products
                </a>
            ';

                                // Status pill
                                $statusClass = ($status === 'Active') ? 'badge-soft-success' : 'badge-soft-warning';
                                $statusHtml  = '<span class="badge ' . $statusClass . '">' . $status . '</span>';

                                // Actions (edit / view / delete – wire up as needed)
                                $actionsHtml = '
                <div class="tableActionButton">
                    <a href="#" data-id="' . base64_encode($catId) . '" data-title="' . $name . '" class="tableActionIconBtn editCategoryPopUp" title="Edit category" data-title="Edit category" target="_blank"><i class="fi fi-rr-edit bgWhite"></i></a>
                    <a href="#" data-type="category" data-id="' . $catId . '" title="Delete category" data-title="Delete category" class="tableActionIconBtn deleteButton" data-redirect="false"><i class="fi fi-rr-trash bgRed"></i></a>
                </div>
            ';

                                echo '<tr>
                <td>
                    <div class="cell-title">
                        <strong>' . $name . '</strong>
                        ' . ($kids > 0 ? '<span class="badge badge-soft-info" title="Immediate child categories">' . $kids . ' sub</span>' : '') . '
                    </div>
                </td>
                <td>' . $parent . '</td>
                <td>' . $productsHtml . '</td>
                <td>' . $statusHtml . '</td>
                <td>' . $actionsHtml . '</td>
            </tr>';
                            }
                        } else {
                            echo '<tr><td colspan="5" class="empty-state">
                <div class="empty">
                    <i class="fi-rr-folder-open"></i>
                    <div>No categories yet.</div>
                </div>
            </td></tr>';
                        }
                        mysqli_free_result($res);
                    } else {
                        echo '<tr><td colspan="5" class="text-danger">Query failed: ' . $esc(mysqli_error($connection)) . '</td></tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </section>
</div>

<?php
// Build parent category options once for the modal
$__esc = fn($s) => htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
$parentOptions = '<option value="">— None (top level) —</option>';
if ($__rs = mysqli_query(
    $connection,
    "SELECT category_id, category_title
         FROM " . TABLE_PREFIX . "category
         WHERE category_status = 1
           AND category_site_id = " . (int)SITE_ID . "
         ORDER BY category_title ASC"
)) {
    while ($__row = mysqli_fetch_assoc($__rs)) {
        $parentOptions .= '<option value="' . (int)$__row['category_id'] . '">' .
            $__esc($__row['category_title']) . '</option>';
    }
    mysqli_free_result($__rs);
}
?>

<!-- Category Modal (Add + Edit) -->
<div class="popUp categoryBox" id="categoryModal" aria-hidden="true" style="display:none;">
    <div class="fullScreenPopUpBox">
        <div class="widget box-7">
            <div class="contentArea">
                <form id="categoryForm" method="POST" action="/dashboard/category/save/">
                    <section class="form">
                        <div class="step-open-box step_1_box">
                            <div class="form-header">
                                <h4 class="heading" id="categoryModalTitle">Add category</h4>
                            </div>

                            <input type="hidden" name="category_id" id="catIdHidden" value="">
                            <div class="form-group">
                                <input type="text" name="category_title" id="catTitleInput" class="form-control"
                                    required>
                                <label class="form-lable" for="catTitleInput">Category title <sup>*</sup></label>
                            </div>

                            <div class="form-group">
                                <select class="selectJS form-control" id="catParentSelect" name="category_parent_id">
                                    <?php echo $parentOptions; ?>
                                </select>
                                <label class="form-lable" for="catParentSelect">Parent category</label>
                            </div>


                            <input type="hidden" name="category_id" id="catIdHidden" value="">
                            <div class="form-group">
                                <textarea name="category_description" id="catDescriptionInput"
                                    class="form-control"></textarea>
                                <label class="form-lable" for="catDescriptionInput">Category description</label>
                            </div>

                        </div>
                    </section>
                </form>
            </div>
        </div>

        <div class="widget box-3">
            <div class="actionArea">
                <button form="categoryForm" type="submit" class="btn popUpBtn btn-natural-green" id="saveCategoryBtn">
                    <i class="fi-rr-check"></i> Save
                </button>
                <span class="btn popUpBtn" id="closeCategoryModal"><i class="fi-rr-cross"></i> Cancel</span>
            </div>
        </div>
    </div>
</div>