<?php

$productStatus = mysqli_real_escape_string($connection, $_GET['status']);
$startFrom = (isset($_GET['product']) ? $_GET['product'] : 0);

$productStatus = (($productStatus == 'all') ? '' : "AND product_status='{$productStatus}'");

?>
<style>
    .product-cell {
        display: flex;
        gap: 12px;
        align-items: flex-start;
    }

    .product-thumb {
        width: 58px;
        height: 58px;
        border-radius: 8px;
        overflow: hidden;
        background: #eef3f7;
        flex-shrink: 0;
        border: 1px solid #d7e0e8;
    }

    .product-thumb img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .product-meta {
        min-width: 0;
    }

    .product-meta .hover {
        display: block !important;
        min-height: 18px;
        margin-top: 8px;
        opacity: 0;
        visibility: hidden;
        transition: opacity 0.15s ease;
    }

    #table tbody tr:hover .product-meta .hover,
    #table tbody tr:focus-within .product-meta .hover {
        opacity: 1;
        visibility: visible;
    }

    .product-meta p {
        margin: 0;
    }

    .product-title-link {
        font-size: 14px;
        font-weight: 600;
        line-height: 1.35;
        display: inline-block;
        max-width: 100%;
        word-break: break-word;
    }

    .product-ids {
        margin-top: 4px;
        color: #637282;
        font-size: 12px;
    }

    .product-tags {
        margin-top: 6px;
        display: flex;
        flex-wrap: wrap;
        gap: 5px;
    }

    .product-tag {
        background: #f2f6fa;
        border: 1px solid #d9e2ea;
        color: #41505f;
        font-size: 11px;
        padding: 2px 6px;
        border-radius: 999px;
        line-height: 1.25;
    }

    .product-tag.status-live {
        background: #e9f9ef;
        border-color: #b8e7c6;
        color: #1e7a37;
    }

    .product-tag.status-draft {
        background: #fff6e8;
        border-color: #f0d7ad;
        color: #8c5a0a;
    }

    .price-main {
        font-weight: 700;
        color: #1f2d3a;
    }

    .price-sub {
        margin-top: 2px;
        color: #6d7b88;
        font-size: 12px;
    }
</style>
<h2>All products</h2>
<div class="options-bar">
    <div class="left-box">
        <form method="GET">
            <input type="hidden" name="type" value="<?php echo $_GET['type']; ?>">
            <select class="form-control" name="status" id="changeStatus">
                <option value="all" <?php echo (($_GET['status'] == 'all') ? 'selected' : '') ?>>All products</option>
                <option value="0" <?php echo (($_GET['status'] == '0') ? 'selected' : '') ?>>Drafts products</option>
                <option value="1" <?php echo (($_GET['status'] == '1') ? 'selected' : '') ?>>Published products</option>
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
            <th style="width: 460px">Product</th>
            <th style="width: 150px">Category</th>
            <th style="width: 130px">Stock</th>
            <th style="width: 120px">Selling Price</th>
            <th style="width: 120px">MRP</th>
            <th style="width: 110px">Publish date</th>
        </tr>
    </thead>
    <tbody>
        <?php

        if ($dataList = mysqli_query($connection, "SELECT product_id, product_title, product_image, product_owner_id, category_title, link_url, link_status, link_creation, inventory_currency_symbol, inventory_in_stock, IFNULL(inventory_sku, '') as inventory_sku, IFNULL(inventory_mrp, 0) as inventory_mrp, IFNULL(inventory_price, 0) as inventory_price FROM " . TABLE_PREFIX . "product INNER JOIN " . TABLE_PREFIX . "category ON category_id=product_category INNER JOIN " . TABLE_PREFIX . "link ON link_relation_id=product_id INNER JOIN " . TABLE_PREFIX . "inventory ON inventory_relation_id=product_id WHERE link_type='product' AND product_type='product' AND link_relation_id=product_id AND link_site_id=" . SITE_ID . " AND category_id=product_category AND link_status!=301 {$productStatus} AND inventory_status=1 AND inventory_type='product' ORDER BY link_published DESC")) {
            if (mysqli_num_rows($dataList) != 0) {
                while ($data = mysqli_fetch_assoc($dataList)) {
                    extract($data);

                    $productImage = trim($product_image) !== '' ? $product_image : ADMIN_IMAGES . 'no-image.png';
                    $stockLabel = ((int)$inventory_in_stock === 1) ? 'In Stock' : 'Out of Stock';
                    $statusLabel = ($link_status == '200') ? 'Published' : 'Draft';
                    $statusClass = ($link_status == '200') ? 'status-live' : 'status-draft';

                    $discountLabel = '';
                    if ((float)$inventory_mrp > 0 && (float)$inventory_price > 0 && (float)$inventory_mrp > (float)$inventory_price) {
                        $discountPercent = round((((float)$inventory_mrp - (float)$inventory_price) / (float)$inventory_mrp) * 100);
                        $discountLabel = $discountPercent . '% off';
                    }
        ?>
                    <tr style="height: 80px;">
                        <td>
                            <div class="product-cell">
                                <div class="product-thumb">
                                    <img src="<?php echo htmlspecialchars($productImage); ?>"
                                        alt="<?php echo htmlspecialchars($product_title); ?>">
                                </div>
                                <div class="product-meta">
                                    <p><a class="product-title-link"
                                            href="product/?type=edit&editor=true&id=<?php echo base64_encode($product_id); ?>"><?php echo $product_title; ?>
                                            <span
                                                class="product-ids"><?php echo (($inventory_sku != '') ? ' | SKU: ' . $inventory_sku : ''); ?>
                                            </span>
                                        </a>
                                    </p>
                                    <div class="product-tags">
                                        <span class="product-tag <?php echo $statusClass; ?>"><?php echo $statusLabel; ?></span>
                                        <span class="product-tag"><?php echo $stockLabel; ?></span>
                                        <?php if ($discountLabel != '') { ?>
                                            <span class="product-tag"><?php echo $discountLabel; ?></span>
                                        <?php } ?>
                                    </div>
                                    <div class="hover">
                                        <a href="product/?type=edit&editor=true&id=<?php echo base64_encode($product_id); ?>"><i
                                                class="fas fa-pencil-alt"></i> Edit</a>
                                        <a href="#" id="<?php echo base64_encode($product_id) ?>_product" class="deleteBtn"><i
                                                class="fas fa-trash"></i> Delete</a>
                                        <a href="<?php echo $link_url; ?>" target='_blank'><i class="fas fa-eye"></i> View</a>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td><?php echo $category_title; ?></td>
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
            "order": [
                [5, "desc"]
            ]
        });
    });
</script>