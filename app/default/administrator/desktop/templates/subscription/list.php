<?php

$productStatus = mysqli_real_escape_string($connection, $_GET['status']);
$startFrom = (isset($_GET['product']) ? $_GET['product'] : 0);

$productStatus = (($productStatus == 'all') ? '' : "AND product_status='{$productStatus}'");

?>
<h2>All Subscriptions</h2>
<div class="options-bar">
    <div class="left-box">
        <form method="GET">
            <input type="hidden" name="type" value="<?php echo $_GET['type']; ?>">
            <select class="form-control" name="status" id="changeStatus">
                <option value="all" <?php echo (($_GET['status'] == 'all') ? 'selected' : '') ?>>All Subscription
                </option>
                <option value="0" <?php echo (($_GET['status'] == '0') ? 'selected' : '') ?>>Drafts Subscription
                </option>
                <option value="1" <?php echo (($_GET['status'] == '1') ? 'selected' : '') ?>>Published Subscription
                </option>
            </select>
            <input type="submit" class="btn" value="Filter">
        </form>
    </div>
    <div class="right-box">

    </div>
</div>
<table id="table" data-default-order-column="5" data-default-order-dir="desc">
    <thead>
        <tr>
            <th style="width: 400px">Title</th>
            <th style="width: 150px">Category</th>
            <th style="width: 150px">In Stock</th>
            <th style="width: 75px">Selling Price</th>
            <th style="width: 75px">MRP</th>
            <th style="width: 100px">Publish date</th>
        </tr>
    </thead>
    <tbody>
        <?php

        if ($dataList = mysqli_query($connection, "SELECT product_id, product_title, product_owner_id, category_title, link_url, link_status, link_published AS published_at, inventory_currency_symbol, inventory_in_stock, IFNULL(inventory_mrp, 0) as inventory_mrp, IFNULL(inventory_price, 0) as inventory_price FROM " . TABLE_PREFIX . "product INNER JOIN " . TABLE_PREFIX . "category ON category_id=product_category INNER JOIN " . TABLE_PREFIX . "link ON link_relation_id=product_id INNER JOIN " . TABLE_PREFIX . "inventory ON inventory_relation_id=product_id WHERE link_type='product' AND product_type='{$postType}' AND link_relation_id=product_id AND link_site_id=" . SITE_ID . " AND category_id=product_category AND link_status!=301 {$productStatus} AND inventory_status=1 AND inventory_type='product' ORDER BY published_at DESC, product_id DESC")) {
            if (mysqli_num_rows($dataList) != 0) {
                while ($data = mysqli_fetch_assoc($dataList)) {
                    extract($data) ?>
        <tr style="height: 80px">
            <td>
                <p><a
                        href="subscription/?type=edit&editor=true&id=<?php echo base64_encode($product_id); ?>"><?php echo $product_title . (($link_status != '200') ? '<strong> — Draft </strong>' : ''); ?></a>
                </p>
                <div class="hover">
                    <a href="subscription/?type=edit&editor=true&id=<?php echo base64_encode($product_id); ?>"><i
                            class="fas fa-pencil-alt"></i> Edit</a>
                    <a href="#" id="<?php echo base64_encode($product_id) ?>_product" class="deleteBtn"><i
                            class="fas fa-trash"></i> Delete</a>
                    <a href="<?php echo $link_url; ?>" target='_blank'><i class="fas fa-eye"></i> View</a>
                </div>
            </td>
            <td><?php echo $category_title; ?></td>
            <td><?php echo (($inventory_in_stock == 1) ? 'Yes' : 'No'); ?></td>
            <td><?php echo $inventory_currency_symbol . ' ' . (($inventory_price != '') ? $inventory_price : 0); ?></td>
            <td><?php echo $inventory_currency_symbol . ' ' . (($inventory_mrp != '') ? $inventory_mrp : 0);; ?></td>
            <td data-order="<?php echo strtotime($published_at); ?>"
                data-sort="<?php echo strtotime($published_at); ?>">
                <?php echo date('d/m/Y H:i:s', strtotime($published_at)); ?></td>
        </tr>
        <?php
                }
            }
        }

        ?>
    </tbody>
</table>
<script>
$(document).ready(function() {
    $('#table').DataTable({
        "bDestroy": true,
        "bStateSave": false,
        "aaSorting": [
            [5, "desc"]
        ],
        "order": [
            [5, "desc"]
        ]
    });
});
</script>