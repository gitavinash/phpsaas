<h2 class="heading">Blogs category list<span>All category list will be displayed here</span>
</h2>
<div class="shortcut">
    <a href="/dashboard/blog/category/"><i class="fi fi-rr-plus"></i></a>
</div>
<div class="main-box">
    <section>
        <div class="filters">
            <div class="form-group">
            </div>
        </div>
        <div class="list-box">
            <table class="showTableList">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Total Articles</th>
                        <th>Published on</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <?php

                if ($supplierList = mysqli_query($connection, "SELECT category_id, category_title, link_url, link_status, category_type, category_status, link_published FROM " . TABLE_PREFIX . "category INNER JOIN " . TABLE_PREFIX . "link ON link_relation_id=category_id WHERE link_status=200 AND link_type='category' AND category_type='post' AND category_site_id=" . SITE_ID . " AND link_site_id=" . SITE_ID)) {
                    echo "<tbody>";
                    if (mysqli_num_rows($supplierList) > 0) {
                        while ($supplierData = mysqli_fetch_assoc($supplierList)) {
                            extract($supplierData);
                            echo "<tr>
                                <td>{$category_title}</td>
                                <td></td>
                                <td>" . date('d-m-Y H:i:s', strtotime($link_published)) . "</td>
                                <td>{$category_status}</td>
                                <td>
                                <a href='/dashboard/blog/category/?id=" . base64_encode($category_id) . "' class='tableActionIconBtn' data-title='Edit blog' ><i class='fi fi-rr-edit bgLightGreen'></i></a>
                                <a href='{$link_url}' class='tableActionIconBtn' data-title='Edit blog' target='_blank' ><i class='fi fi-rr-eye btn-blue'></i></a>
                                <a href='#' data-type='blog' data-id='{$category_id}' data-title='Delete blog' class='tableActionIconBtn deleteButton'><i class='fi fi-rr-trash bgRed'></i></a>
                                </td>
                            </tr>";
                        }
                    }
                    echo "</tbody>";
                }

                ?>
            </table>
        </div>
    </section>
</div>