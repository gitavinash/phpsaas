<?php

$productStatus = mysqli_real_escape_string($connection, $_GET['status']);
$startFrom = (isset($_GET['product']) ? $_GET['product'] : 0);

$productStatus = (($productStatus == 'all') ? '' : "AND product_status='{$productStatus}'");

?>
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
<table id="table">
    <thead>
        <tr>
            <th style="width: 400px">Title</th>
            <th style="width: 150px">Category</th>
            <th style="width: 150px">Brand</th>
            <th style="width: 75px">Selling Price</th>
            <th style="width: 75px">MRP</th>
            <th style="width: 100px">Publish date</th>
        </tr>
    </thead>
    <tbody>
        <?php

        if ($dataList = mysqli_query($connection, "SELECT product_id, product_title, product_owner_id, c1.category_title as category_title, c2.category_title as brand_name, link_url, link_status, link_creation, inventory_currency_symbol, inventory_in_stock, IFNULL(inventory_mrp, 0) as inventory_mrp, IFNULL(inventory_price, 0) as inventory_price FROM " . TABLE_PREFIX . "product LEFT JOIN " . TABLE_PREFIX . "category as c1 ON c1.category_id=product_category INNER JOIN " . TABLE_PREFIX . "link ON link_relation_id=product_id INNER JOIN " . TABLE_PREFIX . "inventory ON inventory_relation_id=product_id LEFT JOIN " . TABLE_PREFIX . "category c2 ON c2.category_id=product_brand_id WHERE link_type='product' AND product_type='packages' AND link_relation_id=product_id AND link_site_id=" . SITE_ID . " AND c1.category_id=product_category AND link_status!=301 {$productStatus} AND inventory_status=1 AND inventory_type='product' ORDER BY link_published DESC")) {
            if (mysqli_num_rows($dataList) != 0) {
                while ($data = mysqli_fetch_assoc($dataList)) {
                    extract($data) ?>
                    <tr style="height: 80px">
                        <td>
                            <p><a href="packages/?type=edit&editor=true&id=<?php echo base64_encode($product_id); ?>"><?php echo $product_title . (($link_status != '200') ? '<strong> — Draft </strong>' : ''); ?></a>
                            </p>
                            <div class="hover">
                                <a href="packages/?type=edit&editor=true&id=<?php echo base64_encode($product_id); ?>"><i class="fas fa-pencil-alt"></i> Edit</a>
                                <a href="#" id="<?php echo base64_encode($product_id) ?>_product" class="deleteBtn"><i class="fas fa-trash"></i> Delete</a>
                                <a href="<?php echo $link_url; ?>" target='_blank'><i class="fas fa-eye"></i> View</a>
                            </div>
                        </td>
                        <td><?php echo $category_title; ?></td>
                        <td><?php echo $brand_name; ?></td>
                        <td><?php echo $inventory_currency_symbol . ' ' . (($inventory_price != '') ? $inventory_price : 0); ?></td>
                        <td><?php echo $inventory_currency_symbol . ' ' . (($inventory_mrp != '') ? $inventory_mrp : 0);; ?></td>
                        <td><?php echo date('d/m/Y', strtotime($link_creation)); ?></td>
                    </tr>
        <?php
                }
            }
        }

        ?>
    </tbody>
</table>