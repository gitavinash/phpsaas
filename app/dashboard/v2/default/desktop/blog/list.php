<h2 class="heading">Blogs list<span>All Blogs list will be displayed here</span>
</h2>
<div class="shortcut">
    <a href="/dashboard/blog/add/"><i class="fi fi-rr-plus"></i></a>
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
                        <th>Category</th>
                        <th>Published on</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <?php

                if ($supplierList = mysqli_query($connection, "SELECT post_id, post_title, link_url, category_title, link_status, post_type, post_status, link_published FROM " . TABLE_PREFIX . "post INNER JOIN " . TABLE_PREFIX . "category ON post_category=category_id AND category_site_id=" . SITE_ID . " INNER JOIN " . TABLE_PREFIX . "link ON link_relation_id=post_id WHERE link_status=200 AND link_type='post' AND post_type='blog' AND post_site_id=" . SITE_ID . " AND link_site_id=" . SITE_ID)) {
                    echo "<tbody>";
                    if (mysqli_num_rows($supplierList) > 0) {
                        while ($supplierData = mysqli_fetch_assoc($supplierList)) {
                            extract($supplierData);
                            echo "<tr>
                                <td>{$post_title}</td>
                                <td>{$category_title}</td>
                                <td>" . date('d-m-Y H:i:s', strtotime($link_published)) . "</td>
                                <td>" . (($post_status == 1) ? 'Published' : '') . "</td>
                                <td>
                                <a href='/dashboard/blog/update/?id=" . base64_encode($post_id) . "' class='tableActionIconBtn' data-title='Edit blog' ><i class='fi fi-rr-edit bgLightGreen'></i></a>
                                <a href='{$link_url}' class='tableActionIconBtn' data-title='Edit blog' target='_blank' ><i class='fi fi-rr-eye btn-blue'></i></a>
                                <a href='#' data-type='blog' data-id='{$post_id}' data-title='Delete blog' class='tableActionIconBtn deleteButton'><i class='fi fi-rr-trash bgRed'></i></a>
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