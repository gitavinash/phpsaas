<?php

$productStatus = mysqli_real_escape_string($connection, $_GET['status']);
$startFrom = (isset($_GET['product']) ? $_GET['product'] : 0);

$productStatus = (($productStatus == 'all') ? '' : "AND product_status='{$productStatus}'");

?>
<link rel="stylesheet" type="text/css" href="<?php echo ADMIN_CSS_LIBRARY ?>admin_new.css">
<script src="<?php echo JS_LIBRARY ?>admin_new.js"></script>
<h2>All Packages Tests</h2>
<div class="options-bar">
    <div class="left-box">
        <form method="GET">
            <input type="hidden" name="type" value="<?php echo $_GET['type']; ?>">
            <select class="form-control" name="status" id="changeStatus">
                <option value="all" <?php echo (($_GET['status'] == 'all') ? 'selected' : '') ?>>All Packages test
                </option>
                <option value="0" <?php echo (($_GET['status'] == '0') ? 'selected' : '') ?>>Drafts Packages test
                </option>
                <option value="1" <?php echo (($_GET['status'] == '1') ? 'selected' : '') ?>>Published Packages test
                </option>
            </select>
            <input type="submit" class="btn" value="Filter">
        </form>
    </div>
    <div class="right-box">

    </div>
</div>
<table id="table" data-default-order-column="6" data-page-length="10">
    <thead>
        <tr>
            <th style="width: 260px">Package</th>
            <th style="width: 120px">Category</th>
            <th style="width: 120px">Brand</th>
            <th style="width: 100px">Stock</th>
            <th style="width: 120px">Selling Price</th>
            <th style="width: 120px">MRP</th>
            <th style="width: 110px">Publish date</th>
            <th style="width: 210px">Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php

        if ($dataList = mysqli_query($connection, "SELECT product_id, product_title, product_owner_id, c1.category_title as category_title, c2.category_title as brand_name, link_url, link_status, link_creation, inventory_currency_symbol, inventory_in_stock, IFNULL(inventory_mrp, 0) as inventory_mrp, IFNULL(inventory_price, 0) as inventory_price FROM " . TABLE_PREFIX . "product LEFT JOIN " . TABLE_PREFIX . "category as c1 ON c1.category_id=product_category INNER JOIN " . TABLE_PREFIX . "link ON link_relation_id=product_id INNER JOIN " . TABLE_PREFIX . "inventory ON inventory_relation_id=product_id LEFT JOIN " . TABLE_PREFIX . "category c2 ON c2.category_id=product_brand_id WHERE link_type='product' AND product_type='packages' AND link_relation_id=product_id AND link_site_id=" . SITE_ID . " AND c1.category_id=product_category AND link_status!=301 {$productStatus} AND inventory_status=1 AND inventory_type='product' ORDER BY link_published DESC")) {
            if (mysqli_num_rows($dataList) != 0) {
                while ($data = mysqli_fetch_assoc($dataList)) {
                    extract($data) ?>
                    <?php
                    $stockLabel = ((int)$inventory_in_stock === 1) ? 'In Stock' : 'Out of Stock';
                    $statusLabel = ($link_status == '200') ? 'Published' : 'Draft';
                    $statusClass = ($link_status == '200') ? 'status-live' : 'status-draft';

                    $discountLabel = '';
                    if ((float)$inventory_mrp > 0 && (float)$inventory_price > 0 && (float)$inventory_mrp > (float)$inventory_price) {
                        $discountPercent = round((((float)$inventory_mrp - (float)$inventory_price) / (float)$inventory_mrp) * 100);
                        $discountLabel = $discountPercent . '% off';
                    }
                    ?>
                    <tr style="height: 80px">
                        <td>
                            <div class="list-cell">
                                <div class="list-meta">
                                    <p><a class="list-title-link" href="packages/?type=edit&editor=true&id=<?php echo base64_encode($product_id); ?>"><?php echo $product_title; ?></a>
                                    </p>
                                    <div class="list-tags">
                                        <span class="list-tag <?php echo $statusClass; ?>"><?php echo $statusLabel; ?></span>
                                        <span class="list-tag"><?php echo $stockLabel; ?></span>
                                        <?php if ($discountLabel != '') { ?>
                                            <span class="list-tag"><?php echo $discountLabel; ?></span>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td><?php echo $category_title; ?></td>
                        <td><?php echo $brand_name; ?></td>
                        <td><?php echo $stockLabel; ?></td>
                        <td>
                            <div class="price-main">
                                <?php echo $inventory_currency_symbol . ' ' . (($inventory_price != '') ? $inventory_price : 0); ?>
                            </div>
                            <?php if ($discountLabel != '') { ?>
                                <div class="price-sub"><?php echo $discountLabel; ?></div>
                            <?php } ?>
                        </td>
                        <td>
                            <div class="price-main">
                                <?php echo $inventory_currency_symbol . ' ' . (($inventory_mrp != '') ? $inventory_mrp : 0); ?>
                            </div>
                        </td>
                        <td data-order="<?php echo strtotime($link_creation); ?>"><?php echo date('d/m/Y', strtotime($link_creation)); ?></td>
                        <td>
                            <div class="action-btns">
                                <a href="packages/?type=edit&editor=true&id=<?php echo base64_encode($product_id); ?>" class="action-btn btn-edit" title="Edit"><i class="fas fa-pencil-alt"></i> Edit</a>
                                <a href="packages/?type=add&editor=true&duplicate_id=<?php echo base64_encode($product_id); ?>" class="action-btn btn-duplicate" title="Duplicate" aria-label="<?php echo htmlspecialchars('Duplicate ' . $product_title, ENT_QUOTES, 'UTF-8'); ?>"><i class="fas fa-copy" aria-hidden="true"></i> Duplicate</a>
                                <a href="<?php echo $link_url; ?>" target="_blank" class="action-btn btn-view" title="View"><i class="fas fa-eye"></i> View</a>
                                <a href="#" id="<?php echo base64_encode($product_id) ?>_product" class="action-btn btn-delete deleteBtn" title="Delete"><i class="fas fa-trash"></i> Delete</a>
                            </div>
                        </td>
                    </tr>
        <?php
                }
            }
        }

        ?>
    </tbody>
</table>